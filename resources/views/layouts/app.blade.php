<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ env('APP_NAME') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/node-waves/waves.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/animate-css/animate.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/morrisjs/morris.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/sweetalert/sweetalert.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/themes/all-themes.css') }}" rel="stylesheet" />
    @stack('styles')
</head>

<body class="theme-red">
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>{{ __('Please wait...') }}</p>
        </div>
    </div>
    <div class="overlay"></div>
    @include('layouts.topbar')
    <section>
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            @include('layouts.accountInfo')
            <!-- #User Info -->
            <!-- Menu -->
            @include('layouts.navbar')
            <!-- #Menu -->
            <!-- Footer -->
            @include('layouts.footer')
            <!-- #Footer -->
        </aside>
    </section>

    <section class="content">
        @yield('content')
    </section>

    <!-- Jquery Core Js -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/jquery-datatable/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendor/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('vendor/node-waves/waves.js') }}"></script>
    <script src="{{ asset('js/lodash.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert/sweetalert.min.js') }}"></script>
    @stack('scripts')
    <script src="{{ asset('js/admin.js') }}"></script>
    <script>
        function setNavigation() {
            var path = window.location.pathname;
            path = path[0] == '/' ? path.substr(1) : path; //it will remove the dash in the URL
            $("ul.list .ml-menu a").each(function() {
                var href = $(this).attr('href');
                var pathHref = href.split('/').slice(3).join('/')

                if (path === pathHref && pathHref != '') {
                    $(this).parent().parent().closest('li').find('a').addClass('toggled');
                    $(this).closest('ul').css('display', 'block');
                }
            });
        }

        $(function() {
            setNavigation();
        });
    </script>
</body>

</html>
