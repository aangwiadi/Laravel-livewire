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

    {{-- Primary KPI cards --}}
    <div class="row">
        <div class="col-sm-6 col-xl-4 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1">Omset</p>
                            <h5 class="fs-18 fw-semibold mb-0">Rp {{ number_format($s->omset, 0, ',', '.') }}</h5>
                        </div>
                        <span class="avatar avatar-lg bg-primary text-white avatar-rounded">
                            <i class="isax isax-wallet-money fs-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1">Laba Kotor</p>
                            <h5 class="fs-18 fw-semibold mb-0">Rp {{ number_format($s->laba_kotor, 0, ',', '.') }}</h5>
                        </div>
                        <span class="avatar avatar-lg bg-success text-white avatar-rounded">
                            <i class="isax isax-money-recive fs-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4 d-flex">
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

    {{-- Secondary detail cards --}}
    <div class="row">
        <div class="col-sm-6 col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <p class="mb-1">Total HPP</p>
                    <h6 class="fs-16 fw-semibold mb-0">Rp {{ number_format($s->total_hpp, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>
        {{-- <div class="col-sm-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <p class="mb-1">Total DPP</p>
                    <h6 class="fs-16 fw-semibold mb-0">Rp {{ number_format($s->total_dpp, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <p class="mb-1">Total PPN</p>
                    <h6 class="fs-16 fw-semibold mb-0">Rp {{ number_format($s->total_ppn, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div> --}}
        <div class="col-sm-6 col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <p class="mb-1">Avg / Transaksi</p>
                    <h6 class="fs-16 fw-semibold mb-0">Rp {{ number_format($s->avg_transaksi, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1">Total Transaksi</p>
                        <h5 class="fs-18 fw-semibold mb-0">{{ number_format($s->total_transaksi) }}</h5>
                    </div>
                    <span class="avatar avatar-lg bg-info text-white avatar-rounded">
                        <i class="isax isax-receipt-2 fs-24"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1">Toko Aktif</p>
                        <h5 class="fs-18 fw-semibold mb-0">{{ number_format($s->toko_aktif) }}</h5>
                    </div>
                    <span class="avatar avatar-lg bg-secondary text-white avatar-rounded">
                        <i class="isax isax-shop fs-24"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Store charts --}}
    <div class="row">
        {{-- Top 5 store performance (omset + laba + margin combo) --}}
        <div class="col-xl-8 d-flex">
            <div class="card flex-fill">
                <div class="card-header">
                    <h5 class="card-title mb-0">Performa 5 KDKMP Terbaik</h5>
                </div>
                <div class="card-body">
                    <div wire:ignore>
                        <div id="storePerformanceChart" style="min-height: 320px;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Margin distribution donut --}}
        <div class="col-xl-4 d-flex">
            <div class="card flex-fill">
                <div class="card-header">
                    <h5 class="card-title mb-0">Distribusi Margin KDKMP</h5>
                </div>
                <div class="card-body">
                    <div wire:ignore>
                        <div id="marginDistributionChart" style="min-height: 320px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Top 5 store omset (horizontal bar) --}}
        <div class="col-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header">
                    <h5 class="card-title mb-0">Omset 5 KDKMP Terbaik</h5>
                </div>
                <div class="card-body">
                    <div wire:ignore>
                        <div id="storeOmsetChart" style="min-height: 320px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Breakdown KPI per toko (table, same filters, paginated + sortable) --}}
    @php $stores = $this->storeBreakdown; @endphp
    <div class="row">
        <div class="col-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">Breakdown KPI per KDKMP</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-13 text-muted">Show</span>
                        <select wire:model.live="perPage" class="form-select form-select-sm" style="width: auto;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive border table-nowrap">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    @php
                                        $columns = [
                                            'name' => 'KDKMP',
                                            'city' => 'Kota',
                                            'omset' => 'Omset',
                                            'total_hpp' => 'HPP',
                                            'laba' => 'Laba',
                                            'margin_pct' => 'Margin %',
                                            'total_transaksi' => 'Transaksi',
                                        ];
                                    @endphp
                                    @foreach ($columns as $key => $label)
                                        <th role="button" wire:click="sortBy('{{ $key }}')" class="user-select-none">
                                            {{ $label }}
                                            @if ($sortField === $key)
                                                <i class="ti ti-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} ms-1"></i>
                                            @else
                                                <i class="ti ti-arrows-sort ms-1 text-muted"></i>
                                            @endif
                                        </th>
                                    @endforeach
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stores as $i => $row)
                                    <tr>
                                        <td>{{ $stores->firstItem() + $i }}</td>
                                        <td class="text-dark fw-medium">{{ $row->name }}</td>
                                        <td>{{ $row->city ?? '-' }}</td>
                                        <td class="text-dark">Rp {{ number_format($row->omset, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row->total_hpp, 0, ',', '.') }}</td>
                                        <td class="text-dark">Rp {{ number_format($row->laba, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge badge-soft-{{ $row->margin_pct >= 0 ? 'success' : 'danger' }} badge-sm">
                                                {{ number_format($row->margin_pct, 2) }}%
                                            </span>
                                        </td>
                                        <td>{{ number_format($row->total_transaksi) }}</td>
                                        <td>
                                            @if ($row->is_active)
                                                <span class="badge badge-soft-success badge-sm">Active</span>
                                            @else
                                                <span class="badge badge-soft-danger badge-sm">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">No stores for the selected filters.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($stores->hasPages())
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
                            <p class="mb-0 text-muted fs-13">
                                Showing {{ $stores->firstItem() }} to {{ $stores->lastItem() }} of {{ $stores->total() }} entries
                            </p>
                            {{ $stores->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
        <script>
            (function () {
                let perfChart, omsetChart, distChart;

                const idr = (v) => new Intl.NumberFormat('id-ID', {
                    notation: 'compact', maximumFractionDigits: 1
                }).format(v);
                const idrFull = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v);

                function renderPerformance(p) {
                    const el = document.querySelector('#storePerformanceChart');
                    if (!el) return;
                    const options = {
                        chart: { type: 'bar', height: 320, toolbar: { show: false } },
                        plotOptions: { bar: { columnWidth: '50%', borderRadius: 4 } },
                        dataLabels: { enabled: false },
                        series: [
                            { name: 'Omset', type: 'column', data: p.omset || [] },
                            { name: 'Laba', type: 'column', data: p.laba || [] },
                            { name: 'Margin %', type: 'line', data: p.margin || [] },
                        ],
                        stroke: { width: [0, 0, 3], curve: 'smooth' },
                        xaxis: { categories: p.storeNames || [] },
                        yaxis: [
                            { title: { text: 'Amount' }, labels: { formatter: idr } },
                            { show: false },
                            { opposite: true, title: { text: 'Margin %' }, min: 0, max: 100, labels: { formatter: (v) => v + '%' } },
                        ],
                        colors: ['#465fff', '#22c55e', '#f59e0b'],
                        legend: { position: 'top' },
                        tooltip: {
                            shared: true, intersect: false,
                            y: { formatter: (v, o) => o.seriesIndex === 2 ? v + '%' : idrFull(v) }
                        }
                    };
                    if (perfChart) {
                        perfChart.updateOptions({ xaxis: { categories: p.storeNames || [] }, series: options.series });
                    } else {
                        perfChart = new ApexCharts(el, options);
                        perfChart.render();
                    }
                }

                function renderOmset(p) {
                    const el = document.querySelector('#storeOmsetChart');
                    if (!el) return;
                    const options = {
                        chart: { type: 'bar', height: 320, toolbar: { show: false } },
                        plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '55%' } },
                        dataLabels: { enabled: true, formatter: idr },
                        series: [{ name: 'Omset', data: p.omset || [] }],
                        xaxis: { categories: p.storeNames || [], labels: { formatter: idr } },
                        colors: ['#465fff'],
                        legend: { show: false },
                        tooltip: { y: { formatter: idrFull } }
                    };
                    if (omsetChart) {
                        omsetChart.updateOptions({ xaxis: { categories: p.storeNames || [] }, series: options.series });
                    } else {
                        omsetChart = new ApexCharts(el, options);
                        omsetChart.render();
                    }
                }

                function renderDist(p) {
                    const el = document.querySelector('#marginDistributionChart');
                    if (!el) return;
                    const options = {
                        chart: { type: 'donut', height: 320 },
                        series: p.distData || [],
                        labels: p.distLabels || [],
                        colors: ['#22c55e', '#465fff', '#f59e0b', '#ef4444', '#94a3b8'],
                        legend: { position: 'bottom' },
                        dataLabels: { enabled: true },
                        tooltip: { y: { formatter: (v) => v + ' toko' } }
                    };
                    if (distChart) {
                        distChart.updateOptions({ series: p.distData || [], labels: p.distLabels || [] });
                    } else {
                        distChart = new ApexCharts(el, options);
                        distChart.render();
                    }
                }

                function renderAll(p) {
                    renderPerformance(p);
                    renderOmset(p);
                    renderDist(p);
                }

                document.addEventListener('livewire:initialized', () => {
                    renderAll(@json($this->chartPayload));

                    Livewire.on('kpi-charts-updated', (e) => {
                        const p = Array.isArray(e) ? e[0] : e;
                        renderAll(p.payload ?? p);
                    });
                });
            })();
        </script>
    @endpush
</div>
