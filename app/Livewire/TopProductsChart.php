<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TopProductsChart extends Component
{
    use WithPagination;

    /** Active period preset: range|week|month|year */
    public string $period = 'month';

    /** Date range bounds (used for all presets; presets just prefill these) */
    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    /** Optional product name filter (partial match) */
    public string $productName = '';

    /** Products table sorting + page size */
    public string $sortField = 'revenue';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public function mount(): void
    {
        $this->applyPeriod('month');
    }

    /**
     * Prefill the date range from a preset, then refresh.
     */
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
                // Keep whatever the user already picked.
                break;
            case 'month':
            default:
                $this->dateFrom = $now->copy()->startOfMonth()->toDateString();
                $this->dateTo = $now->copy()->endOfMonth()->toDateString();
                break;
        }

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

    public function updatedProductName(): void
    {
        $this->resetPage();
        $this->dispatchChartUpdate();
    }

    /**
     * Toggle/Set the products table sort column.
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

    /**
     * Apply a date range pushed from the page-header range picker (#reportrange),
     * which lives outside this component in the admin layout.
     */
    #[On('dashboard-range-changed')]
    public function setDateRange(string $start, string $end): void
    {
        $this->period = 'range';
        $this->dateFrom = $start;
        $this->dateTo = $end;
        $this->resetPage();
        $this->dispatchChartUpdate();
    }

    /**
     * Run the top-5 products aggregation with the active filters.
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    #[Computed]
    public function topProducts()
    {
        $query = DB::table('poskdkmp.orders_items as oi')
            ->join('poskdkmp.orders as o', 'oi.order_id', '=', 'o.id')
            ->join('poskdkmp.products as p', 'oi.product_id', '=', 'p.id')
            ->selectRaw('p.name')
            ->selectRaw('SUM(oi.total_amount) AS revenue')
            ->selectRaw('SUM(oi.total_hpp) AS total_hpp')
            ->selectRaw('SUM(oi.total_amount - oi.total_hpp) AS laba')
            ->selectRaw('ROUND((SUM(oi.total_amount - oi.total_hpp) / NULLIF(SUM(oi.total_amount), 0)) * 100, 2) AS margin_pct')
            ->groupBy('p.id', 'p.name')
            ->orderByDesc('revenue')
            ->limit(5);

        if ($this->dateFrom) {
            $query->whereDate('o.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('o.created_at', '<=', $this->dateTo);
        }

        if ($this->productName !== '') {
            $query->where('p.name', 'ilike', '%'.$this->productName.'%');
        }

        return $query->get();
    }

    /**
     * Period KPI summary (SKU count, qty, revenue, HPP, laba, margin, etc.)
     * sharing the same date / product filters as the chart.
     */
    #[Computed]
    public function summary(): object
    {
        $query = DB::table('poskdkmp.orders_items as oi')
            ->join('poskdkmp.orders as o', 'oi.order_id', '=', 'o.id')
            ->join('poskdkmp.products as p', 'oi.product_id', '=', 'p.id')
            ->selectRaw('COUNT(DISTINCT oi.product_id) AS total_produk_terjual')
            ->selectRaw('COALESCE(SUM(oi.qty), 0) AS total_qty')
            ->selectRaw('COALESCE(SUM(oi.total_amount), 0) AS total_revenue')
            ->selectRaw('COALESCE(SUM(oi.total_hpp), 0) AS total_hpp')
            ->selectRaw('COALESCE(SUM(oi.total_amount - oi.total_hpp), 0) AS laba_kotor')
            ->selectRaw('CASE WHEN SUM(oi.total_amount) > 0 THEN ROUND(SUM(oi.total_amount - oi.total_hpp) / SUM(oi.total_amount) * 100, 2) ELSE 0 END AS margin_pct')
            ->selectRaw('COUNT(DISTINCT o.id) AS total_transaksi')
            ->selectRaw('COALESCE(ROUND(AVG(o.total_amount), 0), 0) AS avg_per_transaksi')
            ->where('oi.total_amount', '>', 0)
            ->where('oi.total_hpp', '>', 0);

        if ($this->dateFrom) {
            $query->whereDate('o.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('o.created_at', '<=', $this->dateTo);
        }

        if ($this->productName !== '') {
            $query->where('p.name', 'ilike', '%'.$this->productName.'%');
        }

        return $query->first() ?? (object) [
            'total_produk_terjual' => 0,
            'total_qty' => 0,
            'total_revenue' => 0,
            'total_hpp' => 0,
            'laba_kotor' => 0,
            'margin_pct' => 0,
            'total_transaksi' => 0,
            'avg_per_transaksi' => 0,
        ];
    }

    /**
     * All products (paginated) with the active filters + table sorting.
     * Uses the same aggregation as the chart, but without the LIMIT 5.
     */
    #[Computed]
    public function products()
    {
        $sortable = ['name', 'revenue', 'total_hpp', 'laba', 'margin_pct'];
        $field = in_array($this->sortField, $sortable, true) ? $this->sortField : 'revenue';
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        $query = DB::table('poskdkmp.orders_items as oi')
            ->join('poskdkmp.orders as o', 'oi.order_id', '=', 'o.id')
            ->join('poskdkmp.products as p', 'oi.product_id', '=', 'p.id')
            ->selectRaw('p.name')
            ->selectRaw('SUM(oi.total_amount) AS revenue')
            ->selectRaw('SUM(oi.total_hpp) AS total_hpp')
            ->selectRaw('SUM(oi.total_amount - oi.total_hpp) AS laba')
            ->selectRaw('ROUND((SUM(oi.total_amount - oi.total_hpp) / NULLIF(SUM(oi.total_amount), 0)) * 100, 2) AS margin_pct')
            ->groupBy('p.id', 'p.name')
            ->orderBy($field, $direction);

        if ($this->dateFrom) {
            $query->whereDate('o.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('o.created_at', '<=', $this->dateTo);
        }

        if ($this->productName !== '') {
            $query->where('p.name', 'ilike', '%'.$this->productName.'%');
        }

        return $query->paginate($this->perPage);
    }

    /**
     * Shape the data for ApexCharts (used for the initial server render).
     *
     * @return array<string, mixed>
     */
    #[Computed]
    public function chartPayload(): array
    {
        $rows = $this->topProducts;

        return [
            'categories' => $rows->pluck('name')->all(),
            'revenue' => $rows->pluck('revenue')->map(fn ($v) => (float) $v)->all(),
            'laba' => $rows->pluck('laba')->map(fn ($v) => (float) $v)->all(),
            'margin' => $rows->pluck('margin_pct')->map(fn ($v) => (float) $v)->all(),
        ];
    }

    /**
     * Shape the data for ApexCharts and push it to the browser.
     */
    public function dispatchChartUpdate(): void
    {
        // Recompute against the latest filter state before pushing to the client.
        unset($this->topProducts, $this->chartPayload, $this->summary);
        $payload = $this->chartPayload;

        $this->dispatch('top-products-updated',
            categories: $payload['categories'],
            revenue: $payload['revenue'],
            laba: $payload['laba'],
            margin: $payload['margin'],
        );
    }

    public function render()
    {
        return view('livewire.top-products-chart');
    }
}
