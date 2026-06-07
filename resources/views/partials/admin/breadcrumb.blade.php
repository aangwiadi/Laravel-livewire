{{--
    Reusable page breadcrumb.
    Set in a child view via section variables:
      @section('page_title', 'Users')
      @section('breadcrumb')
          <li class="breadcrumb-item"><a href="...">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Users</li>
      @endsection
    Right-aligned actions (buttons, date range, etc.):
      @section('page_actions') ... @endsection
--}}
<div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
    <div>
        <h6>@yield('page_title', 'Dashboard')</h6>
        @hasSection('breadcrumb')
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-divide mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        @endif
    </div>
    @hasSection('page_actions')
        <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
            @yield('page_actions')
        </div>
    @endif
</div>
