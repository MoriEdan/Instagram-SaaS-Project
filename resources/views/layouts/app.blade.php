<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Language" content="{{ app()->getLocale() }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#4188c9">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    <title>@yield('title', config('app.name'))</title>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,500,500i,600,600i,700,700i&amp;subset=latin-ext">
    <link rel="stylesheet" href="{{ mix('assets/css/dm.bundle.css') }}">
    @stack('head')
    <script type="text/javascript">
        var BASE_URL = '{{ url('/') }}';
    </script>
</head>

<body>
    <div class="page">
        <div class="page-main">
            <div class="header py-4">
                <div class="container">
                    @include('partials.header')
                </div>
            </div>
            <div class="header collapse d-lg-flex p-0" id="headerMenuCollapse">
                <div class="container">
                    @include('partials.top-menu')
                </div>
            </div>
            <div class="my-3 my-md-5">
                <div class="container">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="list-unstyled mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fe fe-check mr-2"></i> {!! session('success') !!}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            <i class="fe fe-alert-triangle mr-2"></i> {!! session('error') !!}
                        </div>
                    @endif
                    @if(!\Auth::user()->hasVerifiedEmail())
                     {{-- Verify Email --}}
                    @include('auth.verify')
                    @else
                    @yield('content')
                    @endif
                </div>
            </div>
        </div>

        @include('partials.bottom-menu')

        @include('partials.footer')

    </div>

    <script src="{{ mix('assets/js/dm.bundle.js') }}" type="text/javascript"></script>
    @stack('scripts')
</body>

</html>