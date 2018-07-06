@extends ('layouts.app')

@section ('header')
<header class="navbar navbar-expand-md navbar-light bg-white mb-3">
    <nav class="container">
        <!-- Branding Image -->
        <a class="navbar-brand" href="{{ route('index') }}">
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

            @navbarRight
            @endnavbarRight
        </div>
    </nav>
</header>
@endsection

