{{-- Topbar Start --}}
<div class="header">
    <div class="main-header">

        {{-- Logo --}}
        <div class="header-left">
            <a href="{{ url('/') }}" class="logo">
                <img src="{{ asset('assets/img/logo.png') }}" height="30px" alt="Logo">
            </a>
            <a href="{{ url('/') }}" class="dark-logo">
                <img src="{{ asset('assets/img/logo.png') }}" height="30px" alt="Logo">
            </a>
        </div>

        {{-- Sidebar Menu Toggle Button --}}
        <a id="mobile_btn" class="mobile_btn" href="#sidebar">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </a>

        <div class="header-user">
            <div class="nav user-menu nav-list">
                <div class="me-auto d-flex align-items-center" id="header-search">

                    {{-- Search --}}
                    <div class="input-icon-end position-relative me-2">
                        <input type="text" class="form-control" placeholder="Search">
                        <span class="input-icon-addon">
                            <i class="isax isax-search-normal"></i>
                        </span>
                    </div>
                </div>

                <div class="d-flex align-items-center">

                    {{-- Language Dropdown --}}
                    {{-- <div class="nav-item dropdown has-arrow flag-nav me-2">
                        <a class="btn btn-menubar" data-bs-toggle="dropdown" href="javascript:void(0);" role="button">
                            <img src="{{ asset('assets/img/flags/us.svg') }}" alt="Language" class="img-fluid">
                        </a>
                        <ul class="dropdown-menu p-2">
                            <li><a href="javascript:void(0);" class="dropdown-item"><img src="{{ asset('assets/img/flags/us.svg') }}" alt="flag" class="me-2">English</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item"><img src="{{ asset('assets/img/flags/de.svg') }}" alt="flag" class="me-2">German</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item"><img src="{{ asset('assets/img/flags/fr.svg') }}" alt="flag" class="me-2">French</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item"><img src="{{ asset('assets/img/flags/ae.svg') }}" alt="flag" class="me-2">Arabic</a></li>
                        </ul>
                    </div> --}}

                    {{-- Notification --}}
                    {{-- <div class="notification_item me-2">
                        <a href="#" class="btn btn-menubar position-relative" id="notification_popup" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <i class="isax isax-notification-bing5"></i>
                            <span class="position-absolute badge bg-success border border-white"></span>
                        </a>
                        <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg" style="min-height: 300px;">
                            <div class="p-2 border-bottom">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold">Notifications</h6>
                                    </div>
                                    <div class="col-auto">
                                        <div class="dropdown">
                                            <a href="#" class="dropdown-toggle drop-arrow-none link-dark" data-bs-toggle="dropdown" data-bs-offset="0,15" aria-expanded="false">
                                                <i class="isax isax-setting-2 fs-16 text-body align-middle"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-bell-check me-1"></i>Mark as Read</a>
                                                <a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-trash me-1"></i>Delete All</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="notification-body position-relative z-2 rounded-0" data-simplebar>
                                <div class="dropdown-item notification-item py-2 text-wrap border-bottom" id="notification-1">
                                    <div class="d-flex">
                                        <div class="me-2 position-relative flex-shrink-0">
                                            <img src="{{ asset('assets/img/profiles/avatar-05.jpg') }}" class="avatar-md rounded-circle" alt="User Img">
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-0 fw-semibold text-dark">John Smith</p>
                                            <p class="mb-1 text-wrap fs-14">A <span class="fw-semibold">new sale</span> has been recorded.</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fs-12"><i class="isax isax-clock me-1"></i>4 min ago</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2 rounded-bottom border-top text-center">
                                <a href="javascript:void(0);" class="text-center fw-medium fs-14 mb-0">View All</a>
                            </div>
                        </div>
                    </div> --}}

                    {{-- Light/Dark Mode Button --}}
                    <div class="me-2 theme-item">
                        <a href="javascript:void(0);" id="dark-mode-toggle" class="theme-toggle btn btn-menubar">
                            <i class="isax isax-moon"></i>
                        </a>
                        <a href="javascript:void(0);" id="light-mode-toggle" class="theme-toggle btn btn-menubar">
                            <i class="isax isax-sun-1"></i>
                        </a>
                    </div>

                    {{-- User Dropdown --}}
                    <div class="dropdown profile-dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <span class="avatar online">
                                <img src="{{ asset('assets/img/profiles/avatar-01.jpg') }}" alt="Img" class="img-fluid rounded-circle">
                            </span>
                        </a>
                        <div class="dropdown-menu p-2">
                            <div class="d-flex align-items-center bg-light rounded-1 p-2 mb-2">
                                <span class="avatar avatar-lg me-2">
                                    <img src="{{ asset('assets/img/profiles/avatar-01.jpg') }}" alt="img" class="rounded-circle">
                                </span>
                                <div>
                                    <h6 class="fs-14 fw-medium mb-1">{{ auth()->user()->name ?? 'Guest User' }}</h6>
                                    <p class="fs-13">{{ auth()->user()->email ?? 'Administrator' }}</p>
                                </div>
                            </div>
                            <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);">
                                <i class="isax isax-profile-circle me-2"></i>Profile Settings
                            </a>
                            <hr class="dropdown-divider my-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item logout d-flex align-items-center w-100 border-0 bg-transparent">
                                    <i class="isax isax-logout me-2"></i>Sign Out
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div class="dropdown mobile-user-menu profile-dropdown">
            <a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                <span class="avatar avatar-md online">
                    <img src="{{ asset('assets/img/profiles/avatar-01.jpg') }}" alt="Img" class="img-fluid rounded-circle">
                </span>
            </a>
            <div class="dropdown-menu p-2 mt-0">
                <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);">
                    <i class="isax isax-profile-circle me-2"></i>Profile Settings
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item logout d-flex align-items-center w-100 border-0 bg-transparent">
                        <i class="isax isax-logout me-2"></i>Signout
                    </button>
                </form>
            </div>
        </div>
        {{-- /Mobile Menu --}}

    </div>
</div>
{{-- Topbar End --}}
