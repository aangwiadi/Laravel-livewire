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
                <img src="{{ asset('assets/img/logo.svg') }}" alt="Logo">
            </a>
            <a href="{{ url('/') }}" class="logo-small">
                <img src="{{ asset('assets/img/logo-small.svg') }}" alt="Logo">
            </a>
            <a href="{{ url('/') }}" class="dark-logo">
                <img src="{{ asset('assets/img/logo-white.svg') }}" alt="Logo">
            </a>
            <a href="{{ url('/') }}" class="dark-small">
                <img src="{{ asset('assets/img/logo-small-white.svg') }}" alt="Logo">
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

                <div class="sidebar-footer">
                    <div class="trial-item bg-white text-center border">
                        <div class="bg-light p-3 text-center">
                            <img src="{{ asset('assets/img/icons/upgrade.svg') }}" alt="img">
                        </div>
                        <div class="p-2">
                            <h6 class="fs-14 fw-semibold mb-1">Upgrade to More</h6>
                            <p class="fs-13 mb-2">Subscribe to get more with Premium Features</p>
                            <a href="javascript:void(0);" class="btn btn-sm btn-primary w-100 d-flex align-items-center justify-content-center"><i class="isax isax-crown5 me-1"></i>Upgrade</a>
                        </div>
                        <a href="javascript:void(0);" class="close-icon"><i class="fa-solid fa-x"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Sidenav Menu End --}}
