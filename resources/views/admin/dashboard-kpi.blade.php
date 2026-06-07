@extends('layouts.admin')

@section('title', 'KPI Dashboard')
@section('page_title', 'KPI Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item d-flex align-items-center"><a href="{{ url('/') }}"><i class="isax isax-home-2 me-1"></i>Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">KPI Dashboard</li>
@endsection

@section('page_actions')
    <div id="reportrange" class="reportrange-picker d-flex align-items-center" style="cursor: pointer;">
        <i class="isax isax-calendar text-gray-5 fs-14 me-1"></i>
        <span class="reportrange-picker-field">{{ now()->startOfMonth()->format('d M Y') }} - {{ now()->endOfMonth()->format('d M Y') }}</span>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            @livewire('kpi-dashboard')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', function () {
            const el = document.querySelector('#reportrange');
            if (!el || typeof $ === 'undefined' || typeof $.fn.daterangepicker === 'undefined') {
                return;
            }

            const fmt = 'YYYY-MM-DD';
            const labelFmt = 'DD MMM YYYY';
            const start = moment().startOf('month');
            const end = moment().endOf('month');

            function setLabel(s, e) {
                el.querySelector('.reportrange-picker-field').innerHTML =
                    s.format(labelFmt) + ' - ' + e.format(labelFmt);
            }

            $(el).daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'This Week': [moment().startOf('week'), moment().endOf('week')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                }
            }, function (s, e) {
                setLabel(s, e);
                // Push the chosen range to the KPI dashboard component.
                Livewire.dispatch('kpi-range-changed', {
                    start: s.format(fmt),
                    end: e.format(fmt),
                });
            });

            setLabel(start, end);
        });
    </script>
@endpush
