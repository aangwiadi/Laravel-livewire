<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class KpiDashboard extends Component
{
    use WithPagination;

    /** Active period preset: range|week|month|year */
    public string $period = 'month';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    /** Province (companies.state) filter */
    public string $province = '';

    /** City (companies.city) filter */
    public string $city = '';

    /** Store status: '' = all, '1' = active, '0' = inactive */
    public string $isActive = '';

    /** Store breakdown table sorting + page size */
    public string $sortField = 'omset';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public function mount(): void
    {
        $this->applyPeriod('month');
    }

    public function applyPeriod(string $period): void
    {
        $this->period = $period;
        $now = Carbon::now();

        switch ($period) {
            case 'week':
                $this->dateFrom = $now->copy()->startOfWeek()->toDateString();
                $this->dateTo = $now->copy()->endOfWeek()->toDateString();
                break;
            case 'year':
                $this->dateFrom = $now->copy()->startOfYear()->toDateString();
                $this->dateTo = $now->copy()->endOfYear()->toDateString();
                break;
            case 'range':
                break;
            case 'month':
            default:
                $this->dateFrom = $now->copy()->startOfMonth()->toDateString();
                $this->dateTo = $now->copy()->endOfMonth()->toDateString();
                break;
        }

        $this->dispatchChartUpdate();
    }

    public function updatedProvince(): void
    {
        // Reset city when province changes so the city list stays consistent.
        $this->city = '';
        $this->resetPage();
        $this->dispatchChartUpdate();
    }

    public function updatedCity(): void
    {
        $this->resetPage();
        $this->dispatchChartUpdate();
    }

    public function updatedIsActive(): void
    {
        $this->resetPage();
        $this->dispatchChartUpdate();
    }

    public function updatedDateFrom(): void
    {
        $this->period = 'range';
        $this->resetPage();
        $this->dispatchChartUpdate();
    }

    public function updatedDateTo(): void
    {
        $this->period = 'range';
        $this->resetPage();
        $this->dispatchChartUpdate();
    }

    /**
     * Toggle/Set the store breakdown table sort column.
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }

        $this->resetPage();
    }

    #[On('kpi-range-changed')]
    public function setDateRange(string $start, string $end): void
    {
        $this->period = 'range';
        $this->dateFrom = $start;
        $this->dateTo = $end;
        $this->resetPage();
        $this->dispatchChartUpdate();
    }

    /**
     * Distinct provinces (companies.state) for the province dropdown.
     */
    #[Computed]
    public function provinces()
    {
        return DB::table('poskdkmp.companies')
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->distinct()
            ->orderBy('state')
            ->pluck('state');
    }

    /**
     * Distinct cities, optionally constrained by the selected province.
     */
    #[Computed]
    public function cities()
    {
        return DB::table('poskdkmp.companies')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->when($this->province !== '', fn ($q) => $q->where('state', $this->province))
            ->distinct()
            ->orderBy('city')
            ->pluck('city');
    }

    /**
     * KPI summary using the user's query with the active filters applied.
     */
    #[Computed]
    public function summary(): object
    {
        $query = DB::table('poskdkmp.orders as o')
            ->join('poskdkmp.companies as c', 'o.company_id', '=', 'c.id')
            ->selectRaw('COALESCE(SUM(o.total_amount), 0) AS omset')
            ->selectRaw('COALESCE(SUM(o.hpp_amount), 0) AS total_hpp')
            ->selectRaw('COALESCE(SUM(o.dpp_amount), 0) AS total_dpp')
            ->selectRaw('COALESCE(SUM(o.ppn_amount), 0) AS total_ppn')
            ->selectRaw('COALESCE(SUM(o.total_amount - o.hpp_amount), 0) AS laba_kotor')
            ->selectRaw('CASE WHEN SUM(o.total_amount) > 0 THEN ROUND(SUM(o.total_amount - o.hpp_amount) / SUM(o.total_amount) * 100, 2) ELSE 0 END AS margin_pct')
            ->selectRaw('COUNT(o.id) AS total_transaksi')
            ->selectRaw('COUNT(DISTINCT o.company_id) AS toko_aktif')
            ->selectRaw('COALESCE(AVG(o.total_amount), 0) AS avg_transaksi');

        $this->applyFilters($query);

        return $query->first() ?? (object) [
            'omset' => 0, 'total_hpp' => 0, 'total_dpp' => 0, 'total_ppn' => 0,
            'laba_kotor' => 0, 'margin_pct' => 0, 'total_transaksi' => 0,
            'toko_aktif' => 0, 'avg_transaksi' => 0,
        ];
    }

    /**
     * Top 5 stores aggregation (used by the performance + omset charts).
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    #[Computed]
    public function topStores()
    {
        $query = DB::table('poskdkmp.orders as o')
            ->join('poskdkmp.companies as c', 'o.company_id', '=', 'c.id')
            ->selectRaw('c.name')
            ->selectRaw('SUM(o.total_amount) AS omset')
            ->selectRaw('SUM(o.total_amount - o.hpp_amount) AS laba')
            ->selectRaw('ROUND((SUM(o.total_amount - o.hpp_amount) / NULLIF(SUM(o.total_amount), 0)) * 100, 2) AS margin_pct')
            ->groupBy('c.id', 'c.name')
            ->orderByDesc('omset')
            ->limit(5);

        $this->applyFilters($query);

        return $query->get();
    }

    /**
     * Margin distribution buckets across all stores (for the donut chart).
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    #[Computed]
    public function marginDistribution()
    {
        $sub = DB::table('poskdkmp.orders as o')
            ->join('poskdkmp.companies as c', 'o.company_id', '=', 'c.id')
            ->selectRaw('c.id')
            ->selectRaw('ROUND((SUM(o.total_amount - o.hpp_amount) / NULLIF(SUM(o.total_amount), 0)) * 100, 2) AS margin_pct')
            ->groupBy('c.id');

        $this->applyFilters($sub);

        return DB::query()
            ->fromSub($sub, 't')
            ->selectRaw("
                CASE
                    WHEN margin_pct IS NULL THEN 'No Sales'
                    WHEN margin_pct < 10 THEN '< 10%'
                    WHEN margin_pct < 20 THEN '10-20%'
                    WHEN margin_pct < 30 THEN '20-30%'
                    ELSE '>= 30%'
                END AS bucket
            ")
            ->selectRaw('COUNT(*) AS jumlah')
            ->groupBy('bucket')
            ->get();
    }

    /**
     * Combined chart payload for the three store charts.
     *
     * @return array<string, mixed>
     */
    #[Computed]
    public function chartPayload(): array
    {
        $stores = $this->topStores;

        // Keep the distribution buckets in a stable, human order.
        $order = ['>= 30%', '20-30%', '10-20%', '< 10%', 'No Sales'];
        $dist = $this->marginDistribution->keyBy('bucket');

        $distLabels = [];
        $distData = [];
        foreach ($order as $bucket) {
            if (isset($dist[$bucket])) {
                $distLabels[] = $bucket;
                $distData[] = (int) $dist[$bucket]->jumlah;
            }
        }

        return [
            'storeNames' => $stores->pluck('name')->all(),
            'omset' => $stores->pluck('omset')->map(fn ($v) => (float) $v)->all(),
            'laba' => $stores->pluck('laba')->map(fn ($v) => (float) $v)->all(),
            'margin' => $stores->pluck('margin_pct')->map(fn ($v) => (float) $v)->all(),
            'distLabels' => $distLabels,
            'distData' => $distData,
        ];
    }

    /**
     * Push refreshed chart data to the browser after a filter change.
     */
    public function dispatchChartUpdate(): void
    {
        unset($this->summary, $this->topStores, $this->marginDistribution, $this->chartPayload);

        $this->dispatch('kpi-charts-updated', payload: $this->chartPayload);
    }

    /**
     * Per-store KPI breakdown (paginated + sortable), sharing the active filters.
     */
    #[Computed]
    public function storeBreakdown()
    {
        $sortable = ['name', 'city', 'omset', 'total_hpp', 'laba', 'margin_pct', 'total_transaksi'];
        $field = in_array($this->sortField, $sortable, true) ? $this->sortField : 'omset';
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        $query = DB::table('poskdkmp.orders as o')
            ->join('poskdkmp.companies as c', 'o.company_id', '=', 'c.id')
            ->selectRaw('c.name')
            ->selectRaw('c.city')
            ->selectRaw('c.state')
            ->selectRaw('c.is_active')
            ->selectRaw('SUM(o.total_amount) AS omset')
            ->selectRaw('SUM(o.hpp_amount) AS total_hpp')
            ->selectRaw('SUM(o.total_amount - o.hpp_amount) AS laba')
            ->selectRaw('ROUND((SUM(o.total_amount - o.hpp_amount) / NULLIF(SUM(o.total_amount), 0)) * 100, 2) AS margin_pct')
            ->selectRaw('COUNT(o.id) AS total_transaksi')
            ->selectRaw('COALESCE(AVG(o.total_amount), 0) AS avg_transaksi')
            ->groupBy('c.id', 'c.name', 'c.city', 'c.state', 'c.is_active')
            ->orderBy($field, $direction);

        $this->applyFilters($query);

        return $query->paginate($this->perPage);
    }

    /**
     * Apply the shared date / province / city / status filters to a query.
     */
    protected function applyFilters($query): void
    {
        if ($this->dateFrom) {
            $query->whereDate('o.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('o.created_at', '<=', $this->dateTo);
        }

        if ($this->province !== '') {
            $query->where('c.state', $this->province);
        }

        if ($this->city !== '') {
            $query->where('c.city', $this->city);
        }

        if ($this->isActive !== '') {
            $query->where('c.is_active', $this->isActive === '1');
        }
    }

    public function render()
    {
        return view('livewire.kpi-dashboard');
    }
}
