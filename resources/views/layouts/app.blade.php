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
            @yield ('header')

            @yield ('content')

            @yield ('footer')
        </div>

        <!-- Scripts -->
        <script async src="{{ asset('js/app.js') }}"></script>
        @auth
        <script>
            document.getElementById('logout-button').addEventListener('click', (e) => {
                e.preventDefault();
                document.getElementById('logout-form').submit();
            });
        </script>
        @endauth
        <script>
            document.querySelectorAll('a.disabled').forEach((e) => e.addEventListener('click', (e) => e.preventDefault()));
        </script>
    </body>
</html>
