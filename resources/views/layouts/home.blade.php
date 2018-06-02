@extends ('layouts.dashboard')

@section ('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 bg-secondary sidebar collapse" id="homeSidebar">
            <nav class="sidebar-links py-2">
                <div class="list-group list-group-flush">
                    @foreach ($menus as $routeName => $menu)
                    <a class="list-group-item {{ $menu['isCurrent'] ? 'text-body disabled' : 'list-group-item-action list-group-item-dark text-dark' }}{{ $routeName === 'index' ? ' d-md-none d-landscape-none' : '' }}" href="{{ route($routeName) }}">
                        {{ $menu['name'] }}
                    </a>
                    @endforeach
                </div>
            </nav>
        </div>
        <div class="col-12 col-md-10 ml-auto main">
            @if (session('status'))
            <div class="card">
                <h5 class="card-header">Alert</h5>
                <div class="card-body">
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                </div>
            </div>
            @endif

            @yield ('page-content')
        </div>
    </div>
</div>
@endsection
