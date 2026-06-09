<div>
    @php $s = $this->summary; @endphp

    {{-- Filters --}}
    <div class="card">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label fs-13 mb-1">Province</label>
                    <select wire:model.live="province" class="form-select">
                        <option value="">All Provinces</option>
                        @foreach ($this->provinces as $prov)
                            <option value="{{ $prov }}">{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label fs-13 mb-1">City</label>
                    <select wire:model.live="city" class="form-select">
                        <option value="">All Cities</option>
                        @foreach ($this->cities as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label fs-13 mb-1">Store</label>
                    <select wire:model.live="isActive" class="form-select">
                        <option value="">All Stores (KDKMP)</option>
                        <option value="1">Active Only</option>
                        <option value="0">Inactive Only</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label fs-13 mb-1">Period</label>
                    <div class="btn-group w-100" role="group">
                        <button type="button" wire:click="applyPeriod('week')"
                            class="btn btn-sm {{ $period === 'week' ? 'btn-primary' : 'btn-outline-light' }}">Week</button>
                        <button type="button" wire:click="applyPeriod('month')"
                            class="btn btn-sm {{ $period === 'month' ? 'btn-primary' : 'btn-outline-light' }}">Month</button>
                        <button type="button" wire:click="applyPeriod('year')"
                            class="btn btn-sm {{ $period === 'year' ? 'btn-primary' : 'btn-outline-light' }}">Year</button>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label fs-13 mb-1">From</label>
                    <input type="date" class="form-control" wire:model.live="dateFrom" max="{{ $dateTo }}">
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label fs-13 mb-1">To</label>
                    <input type="date" class="form-control" wire:model.live="dateTo" min="{{ $dateFrom }}">
                </div>
            </div>
        </div>
    </div>

    {{-- KPI summary cards --}}
    <div class="row">
        <div class="col-sm-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1">Total Revenue</p>
                            <h5 class="fs-18 fw-semibold mb-0">Rp {{ number_format($s->total_revenue, 0, ',', '.') }}</h5>
                        </div>
                        <span class="avatar avatar-lg bg-primary text-white avatar-rounded">
                            <i class="isax isax-wallet-money fs-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1">Total Laba</p>
                            <h5 class="fs-18 fw-semibold mb-0">Rp {{ number_format($s->total_laba, 0, ',', '.') }}</h5>
                        </div>
                        <span class="avatar avatar-lg bg-success text-white avatar-rounded">
                            <i class="isax isax-money-recive fs-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1">Total Orders</p>
                            <h5 class="fs-18 fw-semibold mb-0">{{ number_format($s->total_orders) }}</h5>
                        </div>
                        <span class="avatar avatar-lg bg-info text-white avatar-rounded">
                            <i class="isax isax-receipt-2 fs-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1">Margin</p>
                            <h5 class="fs-18 fw-semibold mb-0">{{ number_format($s->margin_pct, 2) }}%</h5>
                        </div>
                        <span class="avatar avatar-lg bg-warning text-white avatar-rounded">
                            <i class="isax isax-percentage-circle fs-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Trend line chart --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Trend Penjualan</h5>
            <div class="btn-group" role="group" aria-label="Granularity">
                <button type="button" wire:click="setGranularity('day')"
                    class="btn btn-sm {{ $granularity === 'day' ? 'btn-primary' : 'btn-outline-light' }}">Harian</button>
                <button type="button" wire:click="setGranularity('week')"
                    class="btn btn-sm {{ $granularity === 'week' ? 'btn-primary' : 'btn-outline-light' }}">Mingguan</button>
            </div>
        </div>
        <div class="card-body">
            <div wire:ignore>
                <div id="salesTrendChart" style="min-height: 360px;"></div>
            </div>

            @if ($this->trend->isEmpty())
                <div class="text-center text-muted py-4">No data for the selected period.</div>
            @endif
        </div>
    </div>

    {{-- Cumulative revenue + growth %, split into two charts --}}
    {{-- <div class="row">
        <div class="col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header">
                    <h5 class="card-title mb-0">Revenue Kumulatif</h5>
                </div>
                <div class="card-body">
                    <div wire:ignore>
                        <div id="cumulativeTrendChart" style="min-height: 340px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pertumbuhan (Growth %)</h5>
                </div>
                <div class="card-body">
                    <div wire:ignore>
                        <div id="growthTrendChart" style="min-height: 340px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    @push('scripts')
        <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
        <script>
            (function () {
                let chart;

                const idr = (v) => new Intl.NumberFormat('id-ID', {
                    notation: 'compact', maximumFractionDigits: 1
                }).format(v);
                const idrFull = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v);

                let cumChart;
                let growthChart;

                function buildOptions(p) {
                    return {
                        chart: { type: 'line', height: 360, toolbar: { show: true }, zoom: { enabled: true } },
                        dataLabels: { enabled: false },
                        stroke: { width: [3, 3, 2], curve: 'smooth' },
                        markers: { size: 0, hover: { size: 5 } },
                        series: [
                            { name: 'Revenue', type: 'line', data: p.revenue || [] },
                            { name: 'Laba', type: 'line', data: p.laba || [] },
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
                                { name: 'Revenue', type: 'line', data: p.revenue || [] },
                                { name: 'Laba', type: 'line', data: p.laba || [] },
                                { name: 'Orders', type: 'line', data: p.orders || [] },
                            ]
                        });
                    } else {
                        chart = new ApexCharts(el, buildOptions(p));
                        chart.render();
                    }
                }

                function cumulativeOptions(p) {
                    return {
                        chart: { type: 'area', height: 340, toolbar: { show: true }, zoom: { enabled: true } },
                        dataLabels: { enabled: false },
                        stroke: { width: 3, curve: 'smooth' },
                        series: [
                            { name: 'Revenue Kumulatif', data: p.cumulative || [] },
                        ],
                        fill: {
                            type: 'gradient',
                            gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
                        },
                        xaxis: { type: 'datetime', categories: p.categories || [] },
                        yaxis: { title: { text: 'Revenue Kumulatif' }, labels: { formatter: idr } },
                        colors: ['#465fff'],
                        legend: { show: false },
                        tooltip: { x: { format: 'dd MMM yyyy' }, y: { formatter: idrFull } }
                    };
                }

                function renderCumulative(p) {
                    const el = document.querySelector('#cumulativeTrendChart');
                    if (!el) return;
                    if (cumChart) {
                        cumChart.updateOptions({
                            xaxis: { type: 'datetime', categories: p.categories || [] },
                            series: [{ name: 'Revenue Kumulatif', data: p.cumulative || [] }]
                        });
                    } else {
                        cumChart = new ApexCharts(el, cumulativeOptions(p));
                        cumChart.render();
                    }
                }

                function growthOptions(p) {
                    return {
                        chart: { type: 'bar', height: 340, toolbar: { show: true } },
                        dataLabels: { enabled: false },
                        plotOptions: {
                            bar: {
                                columnWidth: '55%', borderRadius: 3,
                                colors: { ranges: [{ from: -100000, to: 0, color: '#ef4444' }] }
                            }
                        },
                        series: [{ name: 'Growth %', data: p.growth || [] }],
                        xaxis: { type: 'datetime', categories: p.categories || [] },
                        yaxis: { title: { text: 'Growth %' }, labels: { formatter: (v) => v === null ? '-' : v + '%' } },
                        colors: ['#22c55e'],
                        legend: { show: false },
                        tooltip: {
                            x: { format: 'dd MMM yyyy' },
                            y: { formatter: (v) => v === null ? '-' : v + '%' }
                        }
                    };
                }

                function renderGrowth(p) {
                    const el = document.querySelector('#growthTrendChart');
                    if (!el) return;
                    if (growthChart) {
                        growthChart.updateOptions({
                            xaxis: { type: 'datetime', categories: p.categories || [] },
                            series: [{ name: 'Growth %', data: p.growth || [] }]
                        });
                    } else {
                        growthChart = new ApexCharts(el, growthOptions(p));
                        growthChart.render();
                    }
                }

                function renderAll(p) {
                    renderChart(p);
                    renderCumulative(p);
                    renderGrowth(p);
                }

                document.addEventListener('livewire:initialized', () => {
                    renderAll(@json($this->chartPayload));

                    Livewire.on('trend-updated', (e) => {
                        const p = Array.isArray(e) ? e[0] : e;
                        renderAll(p.payload ?? p);
                    });
                });
            })();
        </script>
    @endpush
</div>
