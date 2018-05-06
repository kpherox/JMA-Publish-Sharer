<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>
        @if (View::hasSection('title'))
        @yield('title') -
        @endif
        {{ config('app.name', 'JMA Publish Sharer') }}
        </title>

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <div id="app">
            <header class="m-0 p-0">
                <nav class="navbar navbar-expand-md navbar-light bg-white">
                    <!-- Branding Image -->
                    <a class="navbar-brand col-1 col-md-3 col-xl-2 text-md-center" href="{{ url('/') }}">
                        {{ config('app.name', 'JMA Publish Sharer') }}
                    </a>

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#app-navbar-collapse" aria-controls="#app-navbar-collapse" aria-label="Toggle Navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="app-navbar-collapse">
                        <!-- Left Side Of Navbar -->
                        <ul class="nav navbar-nav">
                            &nbsp;
                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ml-auto">
                            <!-- Authentication Links -->
                            @guest
                            <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                            @else
                            <li class="nav-item dropdown">
                                <a href="#" id="navbarDropdownMenuLink" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    {{ auth()->user()->name }}
                                </a>

                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                    <li class="dropdown-item"><a id="logout-button" class="nav-link" href="{{ route('logout') }}">Logout</a></li>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                    <script>
                                    document.addEventListener('DOMContentLoaded', () => {
                                        document.getElementById('logout-button').addEventListener('click', (e) => {
                                            e.preventDefault();
                                            document.getElementById('logout-form').submit();
                                        });
                                    });
                                    </script>
                                </ul>
                            </li>
                            @endguest
                        </ul>
                    </div>
                </nav>
            </header>

            @yield ('content')
        </div>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
