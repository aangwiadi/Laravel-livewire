<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class SalesTrendChart extends Component
{
    /** Active period preset: range|week|month|year */
    public string $period = 'month';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    /** Time-series granularity: day|week */
    public string $granularity = 'day';

    /** Province (companies.state) filter */
    public string $province = '';

    /** City (companies.city) filter */
    public string $city = '';

    /** Store status: '' = all, '1' = active, '0' = inactive */
    public string $isActive = '';

    public function mount(): void
    {
        $this->applyPeriod('month');
    }

    public function updatedProvince(): void
    {
        $this->city = '';
        $this->dispatchChartUpdate();
    }

    public function updatedCity(): void
    {
        $this->dispatchChartUpdate();
    }

    public function updatedIsActive(): void
    {
        $this->dispatchChartUpdate();
    }

    public function applyPeriod(string $period): void
    {
        $this->period = $period;
        $now = Carbon::now();

        switch ($period) {
            case 'week':
                $this->dateFrom = $now->copy()->startOfWeek()->toDateString();
                $this->dateTo = $now->copy()->endOfWeek()->toDateString();
                $this->granularity = 'day';
                break;
            case 'year':
                $this->dateFrom = $now->copy()->startOfYear()->toDateString();
                $this->dateTo = $now->copy()->endOfYear()->toDateString();
                $this->granularity = 'week';
                break;
            case 'range':
                break;
            case 'month':
            default:
                $this->dateFrom = $now->copy()->startOfMonth()->toDateString();
                $this->dateTo = $now->copy()->endOfMonth()->toDateString();
                $this->granularity = 'day';
                break;
        }

        $this->dispatchChartUpdate();
    }

    public function setGranularity(string $granularity): void
    {
        $this->granularity = in_array($granularity, ['day', 'week'], true) ? $granularity : 'day';
        $this->dispatchChartUpdate();
    }

    public function updatedDateFrom(): void
    {
        $this->period = 'range';
        $this->dispatchChartUpdate();
    }

    public function updatedDateTo(): void
    {
        $this->period = 'range';
        $this->dispatchChartUpdate();
    }

    #[On('trend-range-changed')]
    public function setDateRange(string $start, string $end): void
    {
        $this->period = 'range';
        $this->dateFrom = $start;
        $this->dateTo = $end;
        $this->dispatchChartUpdate();
    }

    /**
     * Sales time-series grouped by day or week, with revenue, laba, and orders.
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    #[Computed]
    public function trend()
    {
        // Gapless time series: build a date spine with generate_series and
        // LEFT JOIN the aggregate so days/weeks without sales show as 0.
        $unit = $this->granularity === 'week' ? 'week' : 'day';
        $step = $unit === 'week' ? '1 week' : '1 day';

        $from = $this->dateFrom ?: Carbon::now()->startOfMonth()->toDateString();
        $to = $this->dateTo ?: Carbon::now()->endOfMonth()->toDateString();

        // Build the store filter (province/city/status) as a parameterised
        // fragment shared by the aggregate CTE.
        [$storeWhere, $storeBindings] = $this->storeFilterSql();

        $sql = "
            WITH date_spine AS (
                SELECT generate_series(
                    date_trunc(?, ?::timestamp),
                    date_trunc(?, ?::timestamp),
                    ?::interval
                )::date AS bucket
            ),
            daily_agg AS (
                SELECT
                    date_trunc(?, o.created_at)::date       AS bucket,
                    SUM(o.total_amount)                     AS revenue,
                    SUM(o.total_amount - o.hpp_amount)      AS laba,
                    COUNT(o.id)                             AS total_orders
                FROM poskdkmp.orders o
                JOIN poskdkmp.companies c ON o.company_id = c.id
                WHERE o.created_at::date BETWEEN ?::date AND ?::date
                {$storeWhere}
                GROUP BY 1
            )
            SELECT
                ds.bucket,
                COALESCE(da.revenue, 0)      AS revenue,
                COALESCE(da.laba, 0)         AS laba,
                COALESCE(da.total_orders, 0) AS total_orders
            FROM date_spine ds
            LEFT JOIN daily_agg da ON da.bucket = ds.bucket
            ORDER BY ds.bucket
        ";

        $bindings = array_merge(
            [$unit, $from, $unit, $to, $step], // spine
            [$unit, $from, $to],               // agg bucket + date filter
            $storeBindings,                    // store filter
        );

        return collect(DB::select($sql, $bindings));
    }

    /**
     * Province / city / status filter as a raw SQL fragment + bindings,
     * for use inside the trend CTE.
     *
     * @return array{0: string, 1: array<int, mixed>}
     */
    protected function storeFilterSql(): array
    {
        $sql = '';
        $bindings = [];

        if ($this->province !== '') {
            $sql .= ' AND c.state = ?';
            $bindings[] = $this->province;
        }

        if ($this->city !== '') {
            $sql .= ' AND c.city = ?';
            $bindings[] = $this->city;
        }

        if ($this->isActive !== '') {
            $sql .= ' AND c.is_active = ?';
            $bindings[] = $this->isActive === '1';
        }

        return [$sql, $bindings];
    }

    /**
     * Distinct provinces for the dropdown.
     */
    #[Computed]
    public function provinces()
    {
        return DB::table('poskdkmp.companies')
            ->whereNotNull('state')->where('state', '!=', '')
            ->distinct()->orderBy('state')->pluck('state');
    }

    /**
     * Distinct cities, constrained by the selected province.
     */
    #[Computed]
    public function cities()
    {
        return DB::table('poskdkmp.companies')
            ->whereNotNull('city')->where('city', '!=', '')
            ->when($this->province !== '', fn ($q) => $q->where('state', $this->province))
            ->distinct()->orderBy('city')->pluck('city');
    }

    /**
     * Period totals for the KPI summary cards (respect the same filters).
     */
    #[Computed]
    public function summary(): object
    {
        $rows = $this->trend;

        $totalRevenue = (float) $rows->sum('revenue');
        $totalLaba = (float) $rows->sum('laba');
        $totalOrders = (int) $rows->sum('total_orders');
        $days = max($rows->count(), 1);

        return (object) [
            'total_revenue' => $totalRevenue,
            'total_laba' => $totalLaba,
            'total_orders' => $totalOrders,
            'avg_per_bucket' => $totalRevenue / $days,
            'margin_pct' => $totalRevenue > 0 ? round($totalLaba / $totalRevenue * 100, 2) : 0,
        ];
    }

    /**
     * Shape the time-series for ApexCharts.
     *
     * @return array<string, mixed>
     */
    #[Computed]
    public function chartPayload(): array
    {
        $rows = $this->trend;

        // Cumulative revenue (running total) + day/week-over-prior growth %,
        // mirroring the SQL window functions (SUM OVER, LAG).
        $cumulative = [];
        $growth = [];
        $running = 0.0;
        $prev = null;

        foreach ($rows as $row) {
            $rev = (float) $row->revenue;
            $running += $rev;
            $cumulative[] = $running;

            if ($prev === null || $prev == 0.0) {
                $growth[] = null;
            } else {
                $growth[] = round(($rev - $prev) / $prev * 100, 2);
            }

            $prev = $rev;
        }

        return [
            'categories' => $rows->pluck('bucket')->map(fn ($d) => (string) $d)->all(),
            'revenue' => $rows->pluck('revenue')->map(fn ($v) => (float) $v)->all(),
            'laba' => $rows->pluck('laba')->map(fn ($v) => (float) $v)->all(),
            'orders' => $rows->pluck('total_orders')->map(fn ($v) => (int) $v)->all(),
            'cumulative' => $cumulative,
            'growth' => $growth,
            'granularity' => $this->granularity,
        ];
    }

    public function dispatchChartUpdate(): void
    {
        unset($this->trend, $this->chartPayload, $this->summary);

        $this->dispatch('trend-updated', payload: $this->chartPayload);
    }

    public function render()
    {
        return view('livewire.sales-trend-chart');
    }
}
