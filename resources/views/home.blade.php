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

            <div class="card">
                <div class="card-header">Social Accounts</div>

                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3 text-right mt-1">Twitter</dt>
                            <dd class="col-sm-9">
                            @if ($socialAccounts->where('provider_name', 'twitter')->exists())
                                @foreach ($socialAccounts->where('provider_name', 'twitter')->get() as $account)
                                <p class="mb-2"><a class="text-body" href="http://twitter.com/{{ $account->account_name }}">
                                    <img class="align-middle rounded-circle social-avatar" src="{{ $account->account_avatar }}" alt="Twitter Icon" />
                                    &#64;{{ $account->account_name }}
                                </a></p>
                                @endforeach
                            @endif
                                <p class="mb-0"><a class="text-body" href="{{ route('twitter.linktouser') }}">
                                    <button type="button" class="btn btn-secondary btn-lg btn-add p-0 rounded-circle">＋</button>
                                    Link Account
                                </a></p>
                            </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
