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

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="{{ route('home.index') }}">{{ auth()->user()->name }}</a>
            <div class="dropdown-divider"></div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item">Logout</button>
            </form>
        </div>
    </li>
    @endguest
</ul>
