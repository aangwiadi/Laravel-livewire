<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class StockAlertDashboard extends Component
{
    use WithPagination;

    /** Location filters */
    public string $state = '';

    public string $city = '';

    public string $companyId = '';

    /** Stock status: '' | HABIS | KRITIS | RENDAH | PERHATIAN */
    public string $status = '';

    /** Threshold qty <= (0, 3, 10, 20) */
    public int $threshold = 10;

    /** Sync period: today|last7|last30|all */
    public string $period = 'last30';

    /** Table search + sort + view */
    public string $search = '';

    public string $sortCol = 'qty_asc';

    public string $view = 'table';

    public int $perPage = 25;

    /** Defer heavy KPI/chart aggregates so the paginated table renders first. */
    public bool $statsLoaded = false;

    /**
     * Triggered via wire:init after the first paint to compute the heavy
     * KPI + chart aggregates in a separate request (keeps initial load fast).
     */
    public function loadStats(): void
    {
        $this->statsLoaded = true;
        $this->dispatchChartUpdate();
    }

    /* -----------------------------------------------------------------
     |  Filter interactions
     | ----------------------------------------------------------------- */

    public function updatedState(): void
    {
        $this->city = '';
        $this->companyId = '';
        $this->resetPage();
        $this->dispatchChartUpdate();
    }

    public function updatedCity(): void
    {
        $this->companyId = '';
        $this->resetPage();
        $this->dispatchChartUpdate();
    }

    public function updatedCompanyId(): void
    {
        $this->resetPage();
        $this->dispatchChartUpdate();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedThreshold(): void
    {
        $this->resetPage();
        $this->dispatchChartUpdate();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSortCol(): void
    {
        $this->resetPage();
    }

    public function setPeriod(string $period): void
    {
        $this->period = in_array($period, ['today', 'last7', 'last30', 'all'], true) ? $period : 'last30';
        $this->resetPage();
        $this->dispatchChartUpdate();
    }

    public function setView(string $view): void
    {
        $this->view = $view === 'chart' ? 'chart' : 'table';
    }

    /* -----------------------------------------------------------------
     |  Helpers
     | ----------------------------------------------------------------- */

    /**
     * Resolve the active sync-date range from the period preset.
     * Returns sargable timestamp bounds: [from 00:00:00, toExclusive 00:00:00).
     *
     * @return array{0: ?string, 1: ?string}
     */
    protected function dateRange(): array
    {
        $today = Carbon::today();

        return match ($this->period) {
            'today' => [$today->toDateTimeString(), $today->copy()->addDay()->toDateTimeString()],
            'last7' => [$today->copy()->subDays(6)->toDateTimeString(), $today->copy()->addDay()->toDateTimeString()],
            'last30' => [$today->copy()->subDays(29)->toDateTimeString(), $today->copy()->addDay()->toDateTimeString()],
            default => [null, null], // all
        };
    }

    /**
     * Apply the sargable sync-date range (avoids ::date cast so an index on
     * synced_at can be used).
     */
    protected function applyDateRange($query): void
    {
        [$from, $to] = $this->dateRange();

        if ($from) {
            $query->where('ps.synced_at', '>=', $from);
        }
        if ($to) {
            $query->where('ps.synced_at', '<', $to);
        }
    }

    /**
     * Build the base stock-alert query (shared by table + KPI + charts).
     * Mirrors the provided SQL: product_stocks JOIN products/companies,
     * LEFT JOIN product_prices, threshold + filters + price guard.
     */
    protected function baseQuery()
    {
        $query = DB::table('poskdkmp.product_stocks as ps')
            ->join('poskdkmp.products as p', 'p.id', '=', 'ps.product_id')
            ->join('poskdkmp.companies as c', 'c.id', '=', 'ps.company_id')
            ->leftJoin('poskdkmp.product_prices as pp', function ($join) {
                $join->on('pp.company_id', '=', 'ps.company_id')
                    ->on('pp.product_id', '=', 'ps.product_id');
            })
            ->where('ps.qty_available', '<=', $this->threshold)
            // exclude produk tanpa data harga (data belum sync)
            ->where('pp.list_price_kdkmp', '>', 1);

        $this->applyDateRange($query);

        if ($this->state !== '') {
            $query->where('c.state', $this->state);
        }
        if ($this->city !== '') {
            $query->where('c.city', $this->city);
        }
        if ($this->companyId !== '') {
            $query->where('ps.company_id', $this->companyId);
        }

        return $query;
    }

    /* -----------------------------------------------------------------
     |  Filter option sources
     | ----------------------------------------------------------------- */

    #[Computed]
    public function states()
    {
        return DB::table('poskdkmp.companies')
            ->whereNotNull('state')->where('state', '!=', '')
            ->distinct()->orderBy('state')->pluck('state');
    }

    #[Computed]
    public function cities()
    {
        return DB::table('poskdkmp.companies')
            ->whereNotNull('city')->where('city', '!=', '')
            ->when($this->state !== '', fn ($q) => $q->where('state', $this->state))
            ->distinct()->orderBy('city')->pluck('city');
    }

    #[Computed]
    public function companies()
    {
        return DB::table('poskdkmp.companies')
            ->when($this->state !== '', fn ($q) => $q->where('state', $this->state))
            ->when($this->city !== '', fn ($q) => $q->where('city', $this->city))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /* -----------------------------------------------------------------
     |  Data
     | ----------------------------------------------------------------- */

    /**
     * KPI counts by status + nilai stok terancam.
     * Uses the same filters (state/city/company/period) but ignores the
     * per-row status/threshold dropdowns so the cards always show the full
     * alert picture (qty <= 20). Forces threshold to 20 for the counts.
     */
    #[Computed]
    public function kpi(): object
    {
        $hargaJual = "CASE WHEN pp.list_price_kdkmp > 0 THEN pp.list_price_kdkmp ELSE pp.list_price END";

        $query = DB::table('poskdkmp.product_stocks as ps')
            ->leftJoin('poskdkmp.product_prices as pp', function ($join) {
                $join->on('pp.company_id', '=', 'ps.company_id')
                    ->on('pp.product_id', '=', 'ps.product_id');
            })
            ->where('ps.qty_available', '<=', 20)
            ->where('pp.list_price_kdkmp', '>', 1)
            ->selectRaw('COUNT(*) FILTER (WHERE ps.qty_available = 0) AS habis')
            ->selectRaw('COUNT(*) FILTER (WHERE ps.qty_available BETWEEN 1 AND 3) AS kritis')
            ->selectRaw('COUNT(*) FILTER (WHERE ps.qty_available BETWEEN 4 AND 10) AS rendah')
            ->selectRaw('COUNT(*) FILTER (WHERE ps.qty_available BETWEEN 11 AND 20) AS perhatian')
            ->selectRaw("COALESCE(SUM(ps.qty_available * ($hargaJual)) FILTER (WHERE ps.qty_available <= 10), 0) AS nilai");

        $this->applyDateRange($query);

        // Only join companies when a location filter is active.
        if ($this->state !== '' || $this->city !== '') {
            $query->join('poskdkmp.companies as c', 'c.id', '=', 'ps.company_id');
            if ($this->state !== '') {
                $query->where('c.state', $this->state);
            }
            if ($this->city !== '') {
                $query->where('c.city', $this->city);
            }
        }
        if ($this->companyId !== '') {
            $query->where('ps.company_id', $this->companyId);
        }

        return $query->first() ?? (object) [
            'habis' => 0, 'kritis' => 0, 'rendah' => 0, 'perhatian' => 0, 'nilai' => 0,
        ];
    }

    /**
     * Paginated detail rows for the table.
     */
    #[Computed]
    public function rows()
    {
        $hargaJual = "CASE WHEN pp.list_price_kdkmp > 0 THEN pp.list_price_kdkmp ELSE pp.list_price END";

        $query = $this->baseQuery()
            ->selectRaw('ps.company_id')
            ->selectRaw('c.name AS company_name')
            ->selectRaw('c.city')
            ->selectRaw('p.name AS product_name')
            ->selectRaw('p.barcode')
            ->selectRaw('p.default_code AS kode_produk')
            ->selectRaw('p.uom_name AS uom')
            ->selectRaw('ps.qty_available AS qty')
            ->selectRaw("CASE
                WHEN ps.qty_available = 0 THEN 'HABIS'
                WHEN ps.qty_available <= 3 THEN 'KRITIS'
                WHEN ps.qty_available <= 10 THEN 'RENDAH'
                WHEN ps.qty_available <= 20 THEN 'PERHATIAN'
                ELSE 'AMAN' END AS status")
            ->selectRaw("($hargaJual) AS price")
            ->selectRaw('pp.avg_hpp AS hpp')
            ->selectRaw("ps.qty_available * ($hargaJual) AS nilai_stok")
            ->selectRaw('ps.synced_at::date AS synced_at');

        // Status dropdown filter
        $statusRanges = [
            'HABIS' => [0, 0],
            'KRITIS' => [1, 3],
            'RENDAH' => [4, 10],
            'PERHATIAN' => [11, 20],
        ];
        if (isset($statusRanges[$this->status])) {
            [$lo, $hi] = $statusRanges[$this->status];
            $query->whereBetween('ps.qty_available', [$lo, $hi]);
        }

        // Search across product / barcode / company
        if ($this->search !== '') {
            $term = '%'.$this->search.'%';
            $query->where(function ($q) use ($term) {
                $q->where('p.name', 'ilike', $term)
                    ->orWhere('p.barcode', 'ilike', $term)
                    ->orWhere('p.default_code', 'ilike', $term)
                    ->orWhere('c.name', 'ilike', $term);
            });
        }

        // Sorting
        match ($this->sortCol) {
            'qty_desc' => $query->orderByDesc('ps.qty_available'),
            'nilai_desc' => $query->orderByRaw("ps.qty_available * ($hargaJual) DESC"),
            'company_asc' => $query->orderBy('c.name'),
            'product_asc' => $query->orderBy('p.name'),
            'synced_desc' => $query->orderByDesc('ps.synced_at'),
            default => $query->orderBy('ps.qty_available')->orderByRaw("ps.qty_available * ($hargaJual) DESC"),
        };

        // simplePaginate avoids a COUNT(*) over millions of rows.
        return $query->simplePaginate($this->perPage);
    }

    /**
     * Chart payload: status per top stores, status distribution, trend by sync date.
     *
     * @return array<string, mixed>
     */
    #[Computed]
    public function chartPayload(): array
    {
        // Distribution (counts by status across the current filters, qty <= 20)
        $k = $this->kpi;
        $dist = [
            'labels' => ['Habis', 'Kritis', 'Rendah', 'Perhatian'],
            'data' => [(int) $k->habis, (int) $k->kritis, (int) $k->rendah, (int) $k->perhatian],
        ];

        // Status per store (top 10 by total alert qty <= 10)
        $byStore = $this->baseQuery()
            ->where('ps.qty_available', '<=', 20)
            ->selectRaw('c.name AS company_name')
            ->selectRaw('COUNT(*) FILTER (WHERE ps.qty_available = 0) AS habis')
            ->selectRaw('COUNT(*) FILTER (WHERE ps.qty_available BETWEEN 1 AND 3) AS kritis')
            ->selectRaw('COUNT(*) FILTER (WHERE ps.qty_available BETWEEN 4 AND 10) AS rendah')
            ->selectRaw('COUNT(*) FILTER (WHERE ps.qty_available <= 10) AS total_alert')
            ->groupBy('c.name')
            ->orderByDesc('total_alert')
            ->limit(10)
            ->get();

        // Trend alert per sync date (total perlu restock, qty 1..10)
        $trend = $this->baseQuery()
            ->where('ps.qty_available', '<=', 20)
            ->selectRaw('ps.synced_at::date AS tanggal')
            ->selectRaw('COUNT(*) FILTER (WHERE ps.qty_available > 0 AND ps.qty_available <= 10) AS total_perlu_restock')
            ->groupByRaw('ps.synced_at::date')
            ->orderByRaw('ps.synced_at::date')
            ->get();

        return [
            'dist' => $dist,
            'storeNames' => $byStore->pluck('company_name')->all(),
            'storeHabis' => $byStore->pluck('habis')->map(fn ($v) => (int) $v)->all(),
            'storeKritis' => $byStore->pluck('kritis')->map(fn ($v) => (int) $v)->all(),
            'storeRendah' => $byStore->pluck('rendah')->map(fn ($v) => (int) $v)->all(),
            'trendDates' => $trend->pluck('tanggal')->map(fn ($d) => (string) $d)->all(),
            'trendData' => $trend->pluck('total_perlu_restock')->map(fn ($v) => (int) $v)->all(),
        ];
    }

    public function dispatchChartUpdate(): void
    {
        // Skip heavy aggregates until stats have been explicitly loaded.
        if (! $this->statsLoaded) {
            return;
        }

        unset($this->kpi, $this->chartPayload);

        $this->dispatch('stock-charts-updated', payload: $this->chartPayload);
    }

    public function render()
    {
        return view('livewire.stock-alert-dashboard');
    }
}
