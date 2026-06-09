<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon --}}
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">

    {{-- Core CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/iconsax.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

</head>

<body class="bg-white">

    {{-- Begin Wrapper --}}
    <div class="main-wrapper auth-bg">

        {{-- Start Content --}}
        <div class="container-fuild">
            <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">

                <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap">
                    <div class="col-lg-4 mx-auto">
                        <form action="{{ route('login.attempt') }}" method="POST"
                            class="d-flex justify-content-center align-items-center">
                            @csrf
                            <div class="d-flex flex-column justify-content-lg-center p-4 p-lg-0 pb-0 flex-fill">
                                <div class="mx-auto mb-5 text-center">
                                    <img src="{{ asset('assets/img/logo.png') }}" class="img-fluid" alt="Logo">
                                </div>
                                <div class="card border-0 p-lg-3 shadow-lg">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <h5 class="mb-2">Sign In</h5>
                                            <p class="mb-0">Please enter below details to access the dashboard</p>
                                        </div>

                                        @if (session('status'))
                                            <div class="alert alert-success">{{ session('status') }}</div>
                                        @endif

                                        <div class="mb-3">
                                            <label class="form-label">Email Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-sms-notification"></i>
                                                </span>
                                                <input type="email" name="email" value="{{ old('email') }}"
                                                    class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                                                    placeholder="Enter Email Address" required autofocus>
                                            </div>
                                            @error('email')
                                                <span class="text-danger fs-13">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <div class="pass-group input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-lock"></i>
                                                </span>
                                                <span class="isax toggle-password isax-eye-slash"></span>
                                                <input type="password" name="password"
                                                    class="pass-inputs form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                                                    placeholder="****************" required>
                                            </div>
                                            @error('password')
                                                <span class="text-danger fs-13">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-check-md mb-0">
                                                    <input class="form-check-input" id="remember_me" type="checkbox" name="remember">
                                                    <label for="remember_me" class="form-check-label mt-0">Remember Me</label>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <a href="javascript:void(0);">Forgot Password</a>
                                            </div>
                                        </div>

                                        <div class="mb-1">
                                            <button type="submit" class="btn bg-primary-gradient text-white w-100">Sign In</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        {{-- End Content --}}

    </div>
    {{-- End Wrapper --}}

    {{-- Core JS --}}
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>

</body>

</html>
