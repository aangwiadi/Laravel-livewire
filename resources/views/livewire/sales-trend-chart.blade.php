<div>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Trend Penjualan</h5>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{-- Granularity toggle --}}
                <div class="btn-group" role="group" aria-label="Granularity">
                    <button type="button" wire:click="setGranularity('day')"
                        class="btn btn-sm {{ $granularity === 'day' ? 'btn-primary' : 'btn-outline-light' }}">Harian</button>
                    <button type="button" wire:click="setGranularity('week')"
                        class="btn btn-sm {{ $granularity === 'week' ? 'btn-primary' : 'btn-outline-light' }}">Mingguan</button>
                </div>

                {{-- Period presets --}}
                <div class="btn-group" role="group" aria-label="Period filter">
                    <button type="button" wire:click="applyPeriod('week')"
                        class="btn btn-sm {{ $period === 'week' ? 'btn-primary' : 'btn-outline-light' }}">Week</button>
                    <button type="button" wire:click="applyPeriod('month')"
                        class="btn btn-sm {{ $period === 'month' ? 'btn-primary' : 'btn-outline-light' }}">Month</button>
                    <button type="button" wire:click="applyPeriod('year')"
                        class="btn btn-sm {{ $period === 'year' ? 'btn-primary' : 'btn-outline-light' }}">Year</button>
                    <button type="button" wire:click="applyPeriod('range')"
                        class="btn btn-sm {{ $period === 'range' ? 'btn-primary' : 'btn-outline-light' }}">Custom</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            {{-- Date range filters --}}
            <div class="row g-2 mb-3">
                <div class="col-sm-3">
                    <label class="form-label fs-13 mb-1">From</label>
                    <input type="date" class="form-control" wire:model.live="dateFrom" max="{{ $dateTo }}">
                </div>
                <div class="col-sm-3">
                    <label class="form-label fs-13 mb-1">To</label>
                    <input type="date" class="form-control" wire:model.live="dateTo" min="{{ $dateFrom }}">
                </div>
            </div>

            {{-- Chart --}}
            <div wire:ignore>
                <div id="salesTrendChart" style="min-height: 360px;"></div>
            </div>

            @if ($this->trend->isEmpty())
                <div class="text-center text-muted py-4">No data for the selected period.</div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
        <script>
            (function () {
                let chart;

                const idr = (v) => new Intl.NumberFormat('id-ID', {
                    notation: 'compact', maximumFractionDigits: 1
                }).format(v);
                const idrFull = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v);

                function buildOptions(p) {
                    return {
                        chart: { type: 'area', height: 360, toolbar: { show: true }, zoom: { enabled: true } },
                        dataLabels: { enabled: false },
                        stroke: { width: [2, 2, 3], curve: 'smooth' },
                        fill: {
                            type: ['gradient', 'gradient', 'solid'],
                            gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
                        },
                        series: [
                            { name: 'Revenue', type: 'area', data: p.revenue || [] },
                            { name: 'Laba', type: 'area', data: p.laba || [] },
                            { name: 'Orders', type: 'line', data: p.orders || [] },
                        ],
                        xaxis: {
                            type: 'datetime',
                            categories: p.categories || [],
                        },
                        yaxis: [
                            { title: { text: 'Amount' }, labels: { formatter: idr } },
                            { show: false },
                            { opposite: true, title: { text: 'Orders' }, labels: { formatter: (v) => Math.round(v) } },
                        ],
                        colors: ['#465fff', '#22c55e', '#f59e0b'],
                        legend: { position: 'top' },
                        tooltip: {
                            shared: true, intersect: false,
                            x: { format: 'dd MMM yyyy' },
                            y: {
                                formatter: (v, o) => o.seriesIndex === 2
                                    ? Math.round(v) + ' orders'
                                    : idrFull(v)
                            }
                        }
                    };
                }

                function renderChart(p) {
                    const el = document.querySelector('#salesTrendChart');
                    if (!el) return;
                    if (chart) {
                        chart.updateOptions({
                            xaxis: { type: 'datetime', categories: p.categories || [] },
                            series: [
                                { name: 'Revenue', type: 'area', data: p.revenue || [] },
                                { name: 'Laba', type: 'area', data: p.laba || [] },
                                { name: 'Orders', type: 'line', data: p.orders || [] },
                            ]
                        });
                    } else {
                        chart = new ApexCharts(el, buildOptions(p));
                        chart.render();
                    }
                }

                document.addEventListener('livewire:initialized', () => {
                    renderChart(@json($this->chartPayload));

                    Livewire.on('trend-updated', (e) => {
                        const p = Array.isArray(e) ? e[0] : e;
                        renderChart(p.payload ?? p);
                    });
                });
            })();
        </script>
    @endpush
</div>
