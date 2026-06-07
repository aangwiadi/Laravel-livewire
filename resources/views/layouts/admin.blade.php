<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Dashboard') | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', config('app.name'))">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon --}}
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">

    {{-- Theme Script (must load before paint to avoid theme flash) --}}
    {{-- <script src="{{ asset('assets/js/theme-script.js') }}"></script> --}}

    {{-- Core CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/iconsax.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    {{-- Page-specific styles --}}
    @stack('styles')

    @livewireStyles
</head>

<body>

    {{-- Begin Wrapper --}}
    <div class="main-wrapper">

        @include('partials.admin.header')

        @include('partials.admin.sidebar')

        {{-- ========================
            Start Page Content
        ========================= --}}
        <div class="page-wrapper">
            <div class="content">

                @include('partials.admin.breadcrumb')

                @yield('content')
                {{ $slot ?? '' }}

            </div>

            @include('partials.admin.footer')
        </div>
        {{-- ======================== End Page Content ========================= --}}

    </div>
    {{-- End Wrapper --}}

    {{-- Core JS --}}
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

    {{-- Page-specific scripts --}}
    @stack('scripts')

    {{-- Custom JS (keep last so theme + menu init after plugins) --}}
    <script src="{{ asset('assets/js/script.js') }}"></script>

    @livewireScripts
</body>

</html>
