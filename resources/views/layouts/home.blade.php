@extends ('layouts.dashboard')

@section ('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-3 col-xl-2 bg-secondary sidebar p-0">
            <nav class="collapse links">
                <div class="list-group list-group-flush m-0 pt-2">
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
        <div class="col-12 col-md-9 col-xl-8 offset-xl-1 p-3">
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
