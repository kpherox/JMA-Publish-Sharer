@extends ('layouts.dashboard')

@section ('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 bg-secondary sidebar collapse p-0 pt-5 d-md-block">
            <nav class="sidebar-links pt-2">
                <div class="list-group list-group-flush">
                    <a class="list-group-item @if ($__env->yieldContent('title') !== 'Dashboard') list-group-item-action list-group-item-dark text-dark @else text-body disabled @endif" href="{{ route('home') }}">Dashboard</a>
                    <a class="list-group-item @if ($__env->yieldContent('title') !== 'Social Accounts') list-group-item-action list-group-item-dark text-dark @else text-body disabled @endif" href="{{ route('home.socialAccounts') }}">Social Accounts</a>
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', () => {
                    [].forEach.call(document.getElementsByClassName('disabled'), (e) => {
                        e.addEventListener('click', (e) => e.preventDefault());
                    });
                });
                </script>
            </nav>
        </div>
        <div class="col-12 col-md-10 ml-auto pt-2 main">
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
