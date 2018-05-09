@extends ('layouts.home')
@section ('title', 'Social Accounts')

@section ('page-content')
<div class="col-lg-5 col-xl-4">
    <div id="account-lists" class="card">
        <h5 class="card-header">Account Lists</h5>
        <div class="card-body">
            <h5>Twitter</h5>
            <div class="list-group list-group-flush">
                @if ($socialAccounts->where('provider_name', 'twitter')->exists())
                @foreach ($socialAccounts->where('provider_name', 'twitter')->get() as $account)
                <a class="list-group-item list-group-item-action" href="#twitter-{{ $account->account_name }}">
                    <img class="align-middle rounded-circle social-avatar" src="{{ $account->account_avatar }}" alt="Twitter Icon" />
                    &#64;{{ $account->account_name }}
                </a>
                @endforeach
                @endif
                <a class="list-group-item list-group-item-action" href="{{ route('twitter.linktouser') }}">
                    <button type="button" class="btn btn-secondary btn-lg btn-add p-0 rounded-circle">＋</button>
                    Link Account
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

