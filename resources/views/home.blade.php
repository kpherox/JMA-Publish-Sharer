@extends ('layouts.app')

@section ('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>

            @php
                $social = auth()->user()->accounts();
            @endphp
            @if ($social->exists())
                <div class="card">
                    <div class="card-header">Social Accounts</div>

                    <div class="card-body">
                        <dl class="row">
                            @foreach ($social->get() as $socialAccount)
                                <dt class="col-sm-3 text-right">{{ $socialAccount->provider_name }}</dt>
                                <dd class="col-sm-9">
                                    <img class="align-middle" src="{{ $socialAccount->account_avatar }}" alt="{{ $socialAccount->provider_name }} Icon" />
                                    {{ $socialAccount->account_name }}
                                </dd>
                            @endforeach
                        </dl>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
