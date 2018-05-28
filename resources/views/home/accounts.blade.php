@extends ('layouts.home')
@section ('title', 'Social Accounts')

@section ('page-content')
<div class="col-lg-5 col-xl-4">
    <div id="account-lists" class="card">
        <h5 class="card-header">Account Lists</h5>
        <div class="card-body">
            @foreach ($socialAccounts as $provider => $accounts)
            <div class="card border-0">
                <h5 class="card-header bg-transparent border-info">{{ $provider }}</h5>
                <div class="list-group list-group-flush">
                    @foreach ($accounts as $account)
                    <a class="list-group-item list-group-item-action" href="#{{ $account->provider_name }}-{{ $account->account_name }}">
                        <img class="align-middle rounded-circle social-avatar" src="{{ $account->account_avatar }}" alt="{{ $provider }} {{ $account->account_name }} Icon" />
                        &#64;{{ $account->account_name }}
                    </a>
                    @endforeach
                    <a class="list-group-item list-group-item-action" href="{{ route($account->provider_name.'.linktouser') }}">
                        <button type="button" class="btn btn-secondary btn-lg btn-add p-0 rounded-circle">＋</button>
                        Link Account
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<div class="col-lg-7 col-xl-8">
</div>
@endsection

