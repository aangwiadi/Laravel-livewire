# Development Rules — Laravel + Livewire Admin

These rules define the standards for all future development in this project. Follow them for every new page, component, and feature.

## Stack

- Laravel 12 (PHP ^8.2)
- Livewire 3
- Spatie Laravel Permission 6 (roles & permissions)
- Laravel Octane (runtime)
- Blade for templating
- Bootstrap 5 + the **Kanakku** admin theme (assets live in `public/assets`)

## Frontend / Theme Standard

The admin UI is standardized on the **Kanakku** HTML template shipped in `public/`
(`admin-dashboard.html`, `login.html`, etc.). These HTML files are the **visual
source of truth**. The Blade layout is a faithful port of that template.

Do NOT introduce a second UI kit (no Tabler, no Tailwind UI components for admin
pages). All admin pages share one look: same navbar, same sidebar, same theme.

### Assets

- All theme assets are under `public/assets/` (`css/style.css`, `css/iconsax.css`,
  `plugins/*`, `js/script.js`, `js/theme-script.js`, fonts, images).
- Reference assets with the `asset()` helper, never hardcoded paths:
  `{{ asset('assets/css/style.css') }}`.
- Icons: use the theme's icon sets — **Iconsax** (`<i class="isax isax-*"></i>`),
  **Tabler Icons** (`ti ti-*`), and **Font Awesome** (`fa-*`). Iconsax is the
  primary set used by the sidebar/header.
- Light/Dark mode is handled by `theme-script.js` (loaded in `<head>`) plus the
  toggle buttons in the header. Don't reimplement theming.

## Layout System

### Main admin layout: `resources/views/layouts/admin.blade.php`

Every authenticated admin page MUST extend this layout. It wires up:

- `<head>` with all theme CSS + `@stack('styles')` + `@livewireStyles`
- `partials/admin/header` (topbar: search, language, notifications, theme toggle, user menu)
- `partials/admin/sidebar` (two-column sidebar menu)
- `partials/admin/breadcrumb` (page title + breadcrumb + right-side actions)
- `@yield('content')` + `{{ $slot }}` (so it works for both `@extends` views and Livewire full-page components)
- `partials/admin/footer`
- All theme JS + `@stack('scripts')` + `@livewireScripts`

### Partials: `resources/views/partials/admin/`

- `header.blade.php` — topbar
- `sidebar.blade.php` — sidebar menu (edit this to add menu items)
- `breadcrumb.blade.php` — page header / breadcrumb
- `footer.blade.php` — footer

Keep layout structure in partials. Do not duplicate navbar/sidebar markup in pages.

### Creating a new admin page (Blade)

```blade
@extends('layouts.admin')

@section('title', 'Users')        {{-- <title> tag --}}
@section('page_title', 'Users')   {{-- breadcrumb heading --}}

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="isax isax-home-2 me-1"></i>Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Users</li>
@endsection

@section('page_actions')
    <a href="#" class="btn btn-primary">Add User</a>
@endsection

@section('content')
    <div class="row">
        {{-- page content, using theme card/grid classes --}}
    </div>
@endsection

@push('scripts')
    <script>/* page-specific JS */</script>
@endpush
```

### Sidebar menu items

Edit `partials/admin/sidebar.blade.php`. Use the `nav_active()` helper for the
active state and reference named routes:

```blade
<li>
    <a href="{{ route('admin.users.index') }}" class="{{ nav_active(['admin.users.*']) }}">
        <i class="isax isax-profile-2user5"></i><span>Users</span>
    </a>
</li>
```

Submenus follow the theme markup: `<li class="submenu">` with `subdrop active`
on the parent `<a>` when a child route is active, and a nested `<ul>`.

## Livewire Conventions

- Components live in `app/Livewire/` (PSR-4 `App\Livewire`), views in
  `resources/views/livewire/`.
- Create with `php artisan make:livewire Admin/UserTable`.
- **Full-page components** render inside the admin layout via the `$slot`:
  ```php
  #[Layout('layouts.admin')]
  #[Title('Users')]
  class UserIndex extends Component { /* ... */ }
  ```
- For breadcrumb/page-title on full-page Livewire components, render the
  breadcrumb markup at the top of the component view (or set sections); the
  layout's `@yield('content')` and `$slot` both render in the content area.
- Keep business logic in the component class; keep the Blade view presentational.
- Use `wire:model.live` only when live updates are needed; default to deferred
  binding for forms.
- Always show validation errors using the theme's `is-invalid` + `text-danger`
  pattern (see `admin/login.blade.php`).

## Routing

- Name every route. Admin routes use the `admin.` prefix
  (e.g. `admin.dashboard`, `admin.users.index`).
- The layout/partials reference named routes defensively
  (`Route::has('login') ? route('login') : url('/login')`); prefer defining the
  named route so the fallback isn't needed.
- Group admin routes behind `auth` middleware (and role middleware where needed)
  once authentication is wired.

## Auth & Permissions

- Use Spatie Laravel Permission for roles/permissions.
- Gate admin sections with middleware: `->middleware(['auth', 'role:admin'])` or
  permission checks `@can(...)` in Blade.
- Never expose admin pages without `auth` middleware.
- The header user menu posts to a named `logout` route; implement it with a POST
  + CSRF (already scaffolded in the header partial).

## Coding Standards

- Run `./vendor/bin/pint` before committing (Laravel Pint is in dev deps).
- Controllers thin; logic in actions/services or Livewire components.
- Use form request classes for non-trivial validation.
- Use `config()` / `env()` correctly: `env()` only inside `config/` files.
- Use `{{ }}` (escaped) for all output; only use `{!! !!}` for trusted HTML.
- Add a CSRF meta tag in layouts (done) for AJAX/Livewire.

## Security

- All forms include `@csrf`.
- Authorize every state-changing action (policies / Spatie permissions).
- Escape output (Blade default). Validate and sanitize all input.
- Don't commit secrets; `.env` stays out of version control.

## Do / Don't

- DO extend `layouts.admin` for every admin page.
- DO put shared chrome in `partials/admin/*`.
- DO match the Kanakku theme markup and classes.
- DON'T add Tabler or a second CSS framework to admin pages.
- DON'T hardcode asset paths — use `asset()`.
- DON'T duplicate navbar/sidebar markup in individual pages.
