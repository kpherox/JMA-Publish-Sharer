@extends ('layouts.app')

@section ('header')
<header>
    <nav class="navbar navbar-expand navbar-light bg-white m-0 p-0 fixed-top">
        <!-- Branding Image -->
        <a class="navbar-brand dashboard col-2 text-white bg-dark pt-3 pb-3 pl-2 mr-3 d-none d-md-inline d-landscape-inline" href="{{ route('index') }}">
            {{ config('app.name', 'JMA Publish Sharer') }}
        </a>

        <a class="navbar-brand dashboard col-2 text-white bg-dark pt-3 pb-3 pl-2 mr-0 d-md-none d-landscape-none collapsed" href="#homeSidebar" data-toggle="collapse" role="button" aria-expanded="true" data-target="#homeSidebar" aria-controls="homeSidebar">
            {{ config('app.name', 'JMA Publish Sharer') }}
        </a>

        <!-- Collapsed Hamburger -->
        <button type="button" class="navbar-toggler bg-white" data-toggle="collapse" data-target="#app-navbar-collapse" aria-controls="#app-navbar-collapse" aria-label="Toggle Navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                &nbsp;
            </ul>

            @navbarRight
            @endnavbarRight
        </div>
    </nav>
</header>
@endsection

