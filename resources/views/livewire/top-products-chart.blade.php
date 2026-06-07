<div>
    {{-- KPI summary cards (share the same date / product filters as the chart below) --}}
    @php $s = $this->summary; @endphp
    <div class="row">
        <div class="col-sm-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1">Total Product (SKU)</p>
                            <h5 class="fs-18 fw-semibold mb-0">{{ number_format($s->total_produk_terjual) }}</h5>
                        </div>
                        <span class="avatar avatar-lg bg-primary text-white avatar-rounded">
                            <i class="isax isax-box fs-24"></i>
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
                            <p class="mb-1">Total Revenue</p>
                            <h5 class="fs-18 fw-semibold mb-0">Rp {{ number_format($s->total_revenue, 0, ',', '.') }}</h5>
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
                            <p class="mb-1">Total Margin</p>
                            <h5 class="fs-18 fw-semibold mb-0">Rp {{ number_format($s->laba_kotor, 0, ',', '.') }}</h5>
                        </div>
                        <span class="avatar avatar-lg bg-warning text-white avatar-rounded">
                            <i class="isax isax-chart-success fs-24"></i>
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
                            <p class="mb-1">Margin %</p>
                            <h5 class="fs-18 fw-semibold mb-0">{{ number_format($s->margin_pct, 2) }}%</h5>
                        </div>
                        <span class="avatar avatar-lg bg-info text-white avatar-rounded">
                            <i class="isax isax-percentage-circle fs-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Top 5 Products by Revenue</h5>

            {{-- Period preset buttons --}}
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

        <div class="card-body">
            {{-- Filters row --}}
            <div class="row g-2 mb-3">
                <div class="col-sm-4">
                    <label class="form-label fs-13 mb-1">From</label>
                    <input type="date" class="form-control" wire:model.live="dateFrom" max="{{ $dateTo }}">
                </div>
                <div class="col-sm-4">
                    <label class="form-label fs-13 mb-1">To</label>
                    <input type="date" class="form-control" wire:model.live="dateTo" min="{{ $dateFrom }}">
                </div>
                <div class="col-sm-4">
                    <label class="form-label fs-13 mb-1">Product Name</label>
                    <input type="text" class="form-control" placeholder="Search product..."
                        wire:model.live.debounce.400ms="productName">
                </div>
            </div>

            {{-- Chart --}}
            <div wire:ignore>
                <div id="topProductsChart" style="min-height: 320px;"></div>
            </div>

            @if ($this->topProducts->isEmpty())
                <div class="text-center text-muted py-4">No data for the selected filters.</div>
            @endif
        </div>
    </div>

    {{-- All products table (same filters, paginated + sortable) --}}
    @php $rows = $this->products; @endphp
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">All Products by SKU</h5>
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
                                    'name' => 'Product',
                                    'revenue' => 'Revenue',
                                    'total_hpp' => 'Total HPP',
                                    'laba' => 'Laba',
                                    'margin_pct' => 'Margin %',
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $row)
                            <tr>
                                <td>{{ $rows->firstItem() + $i }}</td>
                                <td class="text-dark fw-medium">{{ $row->name }}</td>
                                <td class="text-dark">Rp {{ number_format($row->revenue, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($row->total_hpp, 0, ',', '.') }}</td>
                                <td class="text-dark">Rp {{ number_format($row->laba, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-soft-{{ $row->margin_pct >= 0 ? 'success' : 'danger' }} badge-sm">
                                        {{ number_format($row->margin_pct, 2) }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No products for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($rows->hasPages())
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
                    <p class="mb-0 text-muted fs-13">
                        Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ $rows->total() }} entries
                    </p>
                    {{ $rows->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
        <script>
            (function () {
                let chart;

                function buildOptions(payload) {
                    return {
                        chart: { type: 'bar', height: 320, toolbar: { show: false } },
                        plotOptions: {
                            bar: { horizontal: false, columnWidth: '45%', borderRadius: 4 }
                        },
                        dataLabels: { enabled: false },
                        series: [
                            { name: 'Revenue', type: 'column', data: payload.revenue || [] },
                            { name: 'Laba', type: 'column', data: payload.laba || [] },
                            { name: 'Margin %', type: 'line', data: payload.margin || [] },
                        ],
                        stroke: { width: [0, 0, 3], curve: 'smooth' },
                        xaxis: { categories: payload.categories || [] },
                        yaxis: [
                            {
                                title: { text: 'Amount' },
                                labels: {
                                    formatter: (v) => new Intl.NumberFormat('id-ID', {
                                        notation: 'compact', maximumFractionDigits: 1
                                    }).format(v)
                                }
                            },
                            { show: false },
                            {
                                opposite: true,
                                title: { text: 'Margin %' },
                                labels: { formatter: (v) => v + '%' },
                                max: 100, min: 0
                            }
                        ],
                        colors: ['#465fff', '#22c55e', '#f59e0b'],
                        legend: { position: 'top' },
                        tooltip: {
                            shared: true, intersect: false,
                            y: {
                                formatter: (val, opts) => {
                                    if (opts.seriesIndex === 2) return val + '%';
                                    return new Intl.NumberFormat('id-ID').format(val);
                                }
                            }
                        }
                    };
                }

                function renderChart(payload) {
                    const el = document.querySelector('#topProductsChart');
                    if (!el) return;
                    if (chart) {
                        chart.updateOptions({
                            xaxis: { categories: payload.categories || [] },
                            series: [
                                { name: 'Revenue', type: 'column', data: payload.revenue || [] },
                                { name: 'Laba', type: 'column', data: payload.laba || [] },
                                { name: 'Margin %', type: 'line', data: payload.margin || [] },
                            ]
                        });
                    } else {
                        chart = new ApexCharts(el, buildOptions(payload));
                        chart.render();
                    }
                }

                // Initial paint with server-rendered data.
                document.addEventListener('livewire:initialized', () => {
                    renderChart(@json($this->chartPayload));

                    Livewire.on('top-products-updated', (payload) => {
                        // Livewire 3 passes named params as an object (array of one).
                        renderChart(Array.isArray(payload) ? payload[0] : payload);
                    });
                });
            })();
        </script>
    @endpush
</div>
