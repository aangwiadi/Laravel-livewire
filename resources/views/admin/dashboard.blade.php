@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item d-flex align-items-center"><a href="{{ url('/') }}"><i class="isax isax-home-2 me-1"></i>Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('page_actions')
    <div id="reportrange" class="reportrange-picker d-flex align-items-center">
        <i class="isax isax-calendar text-gray-5 fs-14 me-1"></i>
        <span class="reportrange-picker-field">{{ now()->subDays(7)->format('d M y') }} - {{ now()->format('d M y') }}</span>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1">Total Users</p>
                            <h5 class="fs-18 fw-semibold mb-0">0</h5>
                        </div>
                        <span class="avatar avatar-lg bg-primary text-white avatar-rounded">
                            <i class="isax isax-profile-2user fs-24"></i>
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
                            <p class="mb-1">Revenue</p>
                            <h5 class="fs-18 fw-semibold mb-0">$0</h5>
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
                            <p class="mb-1">Orders</p>
                            <h5 class="fs-18 fw-semibold mb-0">0</h5>
                        </div>
                        <span class="avatar avatar-lg bg-warning text-white avatar-rounded">
                            <i class="isax isax-shopping-cart fs-24"></i>
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
                            <p class="mb-1">Pending</p>
                            <h5 class="fs-18 fw-semibold mb-0">0</h5>
                        </div>
                        <span class="avatar avatar-lg bg-info text-white avatar-rounded">
                            <i class="isax isax-clock fs-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Welcome</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        This dashboard uses the standard admin layout
                        (<code>resources/views/layouts/admin.blade.php</code>). Build new admin
                        pages by extending this layout and dropping content into the
                        <code>@@section('content')</code> block.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
