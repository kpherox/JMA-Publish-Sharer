@extends ('layouts.app')

@section ('header')
<header>
    <nav class="navbar navbar-expand navbar-light bg-white m-0 p-0 fixed-top">
        <!-- Branding Image -->
        <a class="navbar-brand dashboard col-2 text-white bg-dark pt-3 pb-3 pl-2 mr-0 mr-md-3" href="{{ url('/') }}">
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

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto pr-1 pr-md-3">
                <!-- Authentication Links -->
                @guest
                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                @else
                <li class="nav-item dropdown">
                    <a href="#" id="navbarDropdownMenuLink" class="nav-link dropdown-toggle p-0" data-toggle="dropdown" aria-expanded="false">
                        <img class="align-middle rounded-circle account" src="https://secure.gravatar.com/avatar?s=40&d=mm" alt="{{ auth()->user()->name }}" />
                    </a>

                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                        <li class="dropdown-item"><a id="logout-button" class="nav-link" href="{{ route('logout') }}">Logout</a></li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </ul>
                </li>
                @endguest
            </ul>
        </div>
    </nav>
</header>
@endsection

