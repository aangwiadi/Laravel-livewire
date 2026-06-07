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
        // PostgreSQL date_trunc gives a clean day/week bucket.
        $unit = $this->granularity === 'week' ? 'week' : 'day';

        $query = DB::table('poskdkmp.orders as o')
            ->selectRaw("date_trunc(?, o.created_at)::date AS bucket", [$unit])
            ->selectRaw('SUM(o.total_amount) AS revenue')
            ->selectRaw('SUM(o.total_amount - o.hpp_amount) AS laba')
            ->selectRaw('COUNT(o.id) AS total_orders')
            ->groupByRaw('1')
            ->orderByRaw('1');

        if ($this->dateFrom) {
            $query->whereDate('o.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('o.created_at', '<=', $this->dateTo);
        }

        return $query->get();
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

        return [
            'categories' => $rows->pluck('bucket')->map(fn ($d) => (string) $d)->all(),
            'revenue' => $rows->pluck('revenue')->map(fn ($v) => (float) $v)->all(),
            'laba' => $rows->pluck('laba')->map(fn ($v) => (float) $v)->all(),
            'orders' => $rows->pluck('total_orders')->map(fn ($v) => (int) $v)->all(),
            'granularity' => $this->granularity,
        ];
    }

    public function dispatchChartUpdate(): void
    {
        unset($this->trend, $this->chartPayload);

        $this->dispatch('trend-updated', payload: $this->chartPayload);
    }

    public function render()
    {
        return view('livewire.sales-trend-chart');
    }
}
