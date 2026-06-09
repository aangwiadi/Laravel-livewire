@extends('layouts.admin')

@section('title', 'Stock Alert Dashboard')
@section('page_title', 'Stock Alert Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item d-flex align-items-center"><a href="{{ url('/') }}"><i class="isax isax-home-2 me-1"></i>Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Stock Alert</li>
@endsection

@section('content')
    <div class="mb-3">
        <p class="text-muted mb-0">Monitoring stok rendah &amp; habis · Sumber: <code>product_stocks</code> sync dari Odoo</p>
    </div>

    <div class="row">
        <div class="col-12">
            @livewire('stock-alert-dashboard')
        </div>
    </div>
@endsection
