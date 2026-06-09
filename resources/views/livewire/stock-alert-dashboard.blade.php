<div wire:init="loadStats">
    {{-- Filters --}}
    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-sm-6 col-lg">
                    <label class="form-label fs-13 mb-1">Provinsi</label>
                    <select wire:model.live="state" class="form-select">
                        <option value="">Semua provinsi</option>
                        @foreach ($this->states as $st)
                            <option value="{{ $st }}">{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-lg">
                    <label class="form-label fs-13 mb-1">Kota</label>
                    <select wire:model.live="city" class="form-select">
                        <option value="">Semua kota</option>
                        @foreach ($this->cities as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-lg">
                    <label class="form-label fs-13 mb-1">KDKMP / Toko</label>
                    <select wire:model.live="companyId" class="form-select">
                        <option value="">Semua toko</option>
                        @foreach ($this->companies as $co)
                            <option value="{{ $co->id }}">{{ $co->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-lg">
                    <label class="form-label fs-13 mb-1">Status stok</label>
                    <select wire:model.live="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="HABIS">Habis (qty = 0)</option>
                        <option value="KRITIS">Kritis (qty 1–3)</option>
                        <option value="RENDAH">Rendah (qty 4–10)</option>
                        <option value="PERHATIAN">Perhatian (qty 11–20)</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg">
                    <label class="form-label fs-13 mb-1">Threshold qty ≤</label>
                    <select wire:model.live="threshold" class="form-select">
                        <option value="0">Hanya Habis (0)</option>
                        <option value="3">Habis + Kritis (≤3)</option>
                        <option value="10">Habis+Kritis+Rendah (≤10)</option>
                        <option value="20">Semua alert (≤20)</option>
                    </select>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fs-12 text-muted">Sync terakhir:</span>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" wire:click="setPeriod('today')"
                            class="btn {{ $period === 'today' ? 'btn-primary' : 'btn-outline-light' }}">Hari ini</button>
                        <button type="button" wire:click="setPeriod('last7')"
                            class="btn {{ $period === 'last7' ? 'btn-primary' : 'btn-outline-light' }}">7 hari</button>
                        <button type="button" wire:click="setPeriod('last30')"
                            class="btn {{ $period === 'last30' ? 'btn-primary' : 'btn-outline-light' }}">30 hari</button>
                        <button type="button" wire:click="setPeriod('all')"
                            class="btn {{ $period === 'all' ? 'btn-primary' : 'btn-outline-light' }}">Semua</button>
                    </div>
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" wire:click="setView('table')"
                        class="btn {{ $view === 'table' ? 'btn-primary' : 'btn-outline-light' }}">Tabel</button>
                    <button type="button" wire:click="setView('chart')"
                        class="btn {{ $view === 'chart' ? 'btn-primary' : 'btn-outline-light' }}">Grafik</button>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI cards --}}
    @if (! $statsLoaded)
        <div class="card">
            <div class="card-body text-center text-muted py-4">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Menghitung ringkasan stok...
            </div>
        </div>
    @else
    @php $k = $this->kpi; @endphp
    <div class="row">
        <div class="col-sm-6 col-xl d-flex">
            <div class="card flex-fill border-start border-danger border-3">
                <div class="card-body">
                    <p class="mb-1 fs-13">🔴 Stok Habis</p>
                    <h4 class="fw-semibold mb-0">{{ number_format($k->habis) }}</h4>
                    <span class="fs-12 text-muted">qty = 0</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl d-flex">
            <div class="card flex-fill border-start border-warning border-3">
                <div class="card-body">
                    <p class="mb-1 fs-13">🟠 Kritis (1–3)</p>
                    <h4 class="fw-semibold mb-0">{{ number_format($k->kritis) }}</h4>
                    <span class="fs-12 text-muted">qty 1–3</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl d-flex">
            <div class="card flex-fill border-start border-3" style="border-color:#eab308 !important">
                <div class="card-body">
                    <p class="mb-1 fs-13">🟡 Rendah (4–10)</p>
                    <h4 class="fw-semibold mb-0">{{ number_format($k->rendah) }}</h4>
                    <span class="fs-12 text-muted">qty 4–10</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl d-flex">
            <div class="card flex-fill border-start border-info border-3">
                <div class="card-body">
                    <p class="mb-1 fs-13">🔵 Perhatian (11–20)</p>
                    <h4 class="fw-semibold mb-0">{{ number_format($k->perhatian) }}</h4>
                    <span class="fs-12 text-muted">qty 11–20</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl d-flex">
            <div class="card flex-fill border-start border-success border-3">
                <div class="card-body">
                    <p class="mb-1 fs-13">💰 Nilai stok terancam</p>
                    <h4 class="fw-semibold mb-0">Rp {{ number_format($k->nilai, 0, ',', '.') }}</h4>
                    <span class="fs-12 text-muted">total nilai alert</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row" @if ($view !== 'chart') style="display:none" @endif>
        <div class="col-xl-4 d-flex">
            <div class="card flex-fill">
                <div class="card-header"><h6 class="card-title mb-0">Status per toko</h6></div>
                <div class="card-body"><div wire:ignore><div id="chartByStore" style="min-height:240px"></div></div></div>
            </div>
        </div>
        <div class="col-xl-4 d-flex">
            <div class="card flex-fill">
                <div class="card-header"><h6 class="card-title mb-0">Distribusi status</h6></div>
                <div class="card-body"><div wire:ignore><div id="chartDist" style="min-height:240px"></div></div></div>
            </div>
        </div>
        <div class="col-xl-4 d-flex">
            <div class="card flex-fill">
                <div class="card-header"><h6 class="card-title mb-0">Trend alert per sync date</h6></div>
                <div class="card-body"><div wire:ignore><div id="chartTrend" style="min-height:240px"></div></div></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Detail table --}}
    <div class="card" @if ($view !== 'table') style="display:none" @endif>
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Detail stock alert</h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <input type="text" class="form-control form-control-sm" style="width:220px"
                    placeholder="Cari produk, barcode, toko..." wire:model.live.debounce.400ms="search">
                <select wire:model.live="sortCol" class="form-select form-select-sm" style="width:auto">
                    <option value="qty_asc">Qty ↑ (terendah dulu)</option>
                    <option value="qty_desc">Qty ↓</option>
                    <option value="nilai_desc">Nilai stok ↓</option>
                    <option value="company_asc">Nama toko A–Z</option>
                    <option value="product_asc">Nama produk A–Z</option>
                    <option value="synced_desc">Sync terbaru</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive border table-nowrap" style="max-height:460px;overflow-y:auto">
                <table class="table m-0">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Toko</th>
                            <th>Kota</th>
                            <th>Produk</th>
                            <th>Barcode</th>
                            <th>UOM</th>
                            <th>Qty tersedia</th>
                            <th>Status</th>
                            <th class="text-end">Harga jual</th>
                            <th class="text-end">HPP</th>
                            <th class="text-end">Nilai stok</th>
                            <th>Sync terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->rows as $i => $row)
                            <tr>
                                <td>{{ $this->rows->firstItem() + $i }}</td>
                                <td class="fw-medium">{{ $row->company_name ?? '-' }}</td>
                                <td>{{ $row->city ?? '-' }}</td>
                                <td>{{ $row->product_name ?? '-' }}</td>
                                <td>{{ $row->barcode ?? '-' }}</td>
                                <td>{{ $row->uom ?? '-' }}</td>
                                <td>{{ number_format($row->qty ?? 0) }}</td>
                                <td>
                                    @php $stt = $row->status ?? ''; @endphp
                                    <span class="badge badge-sm
                                        @switch($stt)
                                            @case('HABIS') badge-soft-danger @break
                                            @case('KRITIS') badge-soft-warning @break
                                            @case('RENDAH') badge-soft-warning @break
                                            @case('PERHATIAN') badge-soft-info @break
                                            @default badge-soft-secondary
                                        @endswitch">{{ $stt ?: '-' }}</span>
                                </td>
                                <td class="text-end">Rp {{ number_format($row->price ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($row->hpp ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($row->nilai_stok ?? 0, 0, ',', '.') }}</td>
                                <td>{{ $row->synced_at ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">
                                    Belum ada data. Query akan ditambahkan setelah ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
                <p class="mb-0 text-muted fs-13">
                    @if ($this->rows->count())
                        Showing {{ $this->rows->firstItem() }} to {{ $this->rows->lastItem() }}
                    @else
                        Tidak ada data
                    @endif
                </p>
                {{ $this->rows->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
        <script>
            (function () {
                let byStoreChart, distChart, trendChart;

                function renderByStore(p) {
                    const el = document.querySelector('#chartByStore');
                    if (!el) return;
                    const opts = {
                        chart: { type: 'bar', height: 240, stacked: true, toolbar: { show: false } },
                        plotOptions: { bar: { horizontal: true, borderRadius: 2, barHeight: '70%' } },
                        dataLabels: { enabled: false },
                        series: [
                            { name: 'Habis', data: p.storeHabis || [] },
                            { name: 'Kritis', data: p.storeKritis || [] },
                            { name: 'Rendah', data: p.storeRendah || [] },
                        ],
                        xaxis: { categories: p.storeNames || [] },
                        colors: ['#ef4444', '#f59e0b', '#eab308'],
                        legend: { position: 'top' },
                    };
                    if (byStoreChart) {
                        byStoreChart.updateOptions({ xaxis: { categories: p.storeNames || [] }, series: opts.series });
                    } else {
                        byStoreChart = new ApexCharts(el, opts);
                        byStoreChart.render();
                    }
                }

                function renderDist(p) {
                    const el = document.querySelector('#chartDist');
                    if (!el) return;
                    const d = p.dist || { labels: [], data: [] };
                    const opts = {
                        chart: { type: 'donut', height: 240 },
                        series: d.data || [],
                        labels: d.labels || [],
                        colors: ['#ef4444', '#f59e0b', '#eab308', '#3b82f6'],
                        legend: { position: 'bottom' },
                    };
                    if (distChart) {
                        distChart.updateOptions({ series: d.data || [], labels: d.labels || [] });
                    } else {
                        distChart = new ApexCharts(el, opts);
                        distChart.render();
                    }
                }

                function renderTrend(p) {
                    const el = document.querySelector('#chartTrend');
                    if (!el) return;
                    const opts = {
                        chart: { type: 'line', height: 240, toolbar: { show: false } },
                        stroke: { width: 3, curve: 'smooth' },
                        dataLabels: { enabled: false },
                        series: [{ name: 'Perlu Restock', data: p.trendData || [] }],
                        xaxis: { type: 'datetime', categories: p.trendDates || [] },
                        colors: ['#465fff'],
                    };
                    if (trendChart) {
                        trendChart.updateOptions({ xaxis: { type: 'datetime', categories: p.trendDates || [] }, series: opts.series });
                    } else {
                        trendChart = new ApexCharts(el, opts);
                        trendChart.render();
                    }
                }

                function renderAll(p) {
                    renderByStore(p);
                    renderDist(p);
                    renderTrend(p);
                }

                document.addEventListener('livewire:initialized', () => {
                    // Charts are populated by loadStats() via the event below,
                    // so the heavy aggregates run in a deferred request.
                    Livewire.on('stock-charts-updated', (e) => {
                        const p = Array.isArray(e) ? e[0] : e;
                        renderAll(p.payload ?? p);
                    });
                });
            })();
        </script>
    @endpush
</div>
