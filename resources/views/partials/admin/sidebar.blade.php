@php
    // Sidebar active-state helper. Defined here (guarded) so it is always
    // available when the sidebar renders, independent of provider boot or
    // Octane worker state. Usage: class="{{ nav_active(['admin.users.*']) }}"
    if (! function_exists('nav_active')) {
        function nav_active(array $patterns, string $class = 'active'): string
        {
            return request()->routeIs(...$patterns) ? $class : '';
        }
    }
@endphp

{{-- Sidenav Menu Start --}}
{{-- nav_active() helper is registered in App\Providers\AppServiceProvider --}}
<div class="two-col-sidebar" id="two-col-sidebar">
    <div class="twocol-mini">
        <ul class="menu-list">
            <li>
                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Settings"><i class="isax isax-setting-25"></i></a>
            </li>
            <li>
                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Documentation"><i class="isax isax-document-normal4"></i></a>
            </li>
        </ul>
    </div>

    <div class="sidebar" id="sidebar-two">

        {{-- Start Logo --}}
        <div class="sidebar-logo">
            <a href="{{ url('/') }}" class="logo logo-normal">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
            </a>
            <a href="{{ url('/') }}" class="logo-small">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
            </a>
            <a href="{{ url('/') }}" class="dark-logo">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
            </a>
            <a href="{{ url('/') }}" class="dark-small">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
            </a>

            {{-- Sidebar Hover Menu Toggle Button --}}
            <a id="toggle_btn" href="javascript:void(0);">
                <i class="isax isax-menu-1"></i>
            </a>
        </div>
        {{-- End Logo --}}

        {{-- Search --}}
        <div class="sidebar-search">
            <div class="input-icon-end position-relative">
                <input type="text" class="form-control" placeholder="Search">
                <span class="input-icon-addon">
                    <i class="isax isax-search-normal"></i>
                </span>
            </div>
        </div>
        {{-- /Search --}}

        {{-- Sidenav Menu --}}
        <div class="sidebar-inner" data-simplebar>
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="menu-title"><span>Main</span></li>
                    <li>
                        <ul>
                            <li>
                                <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/') }}"
                                    class="{{ nav_active(['admin.dashboard']) }}">
                                    <i class="isax isax-element-45"></i><span>Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ Route::has('admin.dashboard.kpi') ? route('admin.dashboard.kpi') : url('/dashboard-kpi') }}"
                                    class="{{ nav_active(['admin.dashboard.kpi']) }}">
                                    <i class="isax isax-chart-21"></i><span>KPI Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ Route::has('admin.dashboard.trend') ? route('admin.dashboard.trend') : url('/dashboard-trend') }}"
                                    class="{{ nav_active(['admin.dashboard.trend']) }}">
                                    <i class="isax isax-trend-up"></i><span>Trend Penjualan</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ Route::has('admin.dashboard.stok-alert') ? route('admin.dashboard.stok-alert') : url('/dashboard-stok-alert') }}"
                                    class="{{ nav_active(['admin.dashboard.stok-alert']) }}">
                                    <i class="isax isax-box-15"></i><span>Stock Alert</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-title"><span>Manage</span></li>
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="{{ nav_active(['admin.users.*', 'admin.roles.*'], 'subdrop active') }}">
                                    <i class="isax isax-profile-2user5"></i><span>Manage Users</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="javascript:void(0);" class="{{ nav_active(['admin.users.*']) }}">Users</a></li>
                                    <li><a href="javascript:void(0);" class="{{ nav_active(['admin.roles.*']) }}">Roles &amp; Permissions</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-title"><span>Administration</span></li>
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="{{ nav_active(['admin.settings.*'], 'subdrop active') }}">
                                    <i class="isax isax-setting-25"></i><span>Settings</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="javascript:void(0);">Account Settings</a></li>
                                    <li><a href="javascript:void(0);">Company Settings</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>

        
            </div>
        </div>
    </div>
</div>
{{-- Sidenav Menu End --}}
