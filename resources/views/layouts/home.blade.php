@extends ('layouts.dashboard')

@section ('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 bg-secondary sidebar collapse" id="homeSidebar">
            <nav class="sidebar-links py-2">
                <div class="list-group list-group-flush">
                    <a class="list-group-item @if ($__env->yieldContent('title') !== 'Dashboard') list-group-item-action list-group-item-dark text-dark @else text-body disabled @endif" href="{{ route('home') }}">Dashboard</a>
                    <a class="list-group-item @if ($__env->yieldContent('title') !== 'Social Accounts') list-group-item-action list-group-item-dark text-dark @else text-body disabled @endif" href="{{ route('home.socialAccounts') }}">Social Accounts</a>
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
