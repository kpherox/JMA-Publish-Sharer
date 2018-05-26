@extends ('layouts.home')
@section ('title', 'Social Accounts')

@section ('page-content')
<div class="col-lg-5 col-xl-4">
    <div id="account-lists" class="card">
        <h5 class="card-header">Account Lists</h5>
        <div class="card-body">
            <div class="card border-light">
                <h5 class="card-header bg-transparent border-success">Twitter</h5>
                <div class="list-group list-group-flush">
                    @foreach ($twitterAccounts as $account)
                    <a class="list-group-item list-group-item-action" href="#twitter-{{ $account->account_name }}">
                        <img class="align-middle rounded-circle social-avatar" src="{{ $account->account_avatar }}" alt="Twitter Icon" />
                        &#64;{{ $account->account_name }}
                    </a>
                    @endforeach
                    <a class="list-group-item list-group-item-action" href="{{ route('twitter.linktouser') }}">
                        <button type="button" class="btn btn-secondary btn-lg btn-add p-0 rounded-circle">＋</button>
                        Link Account
                    </a>
                </div>
            </div>
            <div class="card border-light">
                <h5 class="card-header bg-transparent border-success">GitHub</h5>
                <div class="list-group list-group-flush">
                    @if ($account = $githubAccounts->first())
                    <a class="list-group-item list-group-item-action" href="#twitter-{{ $account->account_name }}">
                        <img class="align-middle rounded-circle social-avatar" src="{{ $account->account_avatar }}" alt="GitHub Icon" />
                        &#64;{{ $account->account_name }}
                    </a>
                    @else
                    <a class="list-group-item list-group-item-action" href="{{ route('github.linktouser') }}">
                        <button type="button" class="btn btn-secondary btn-lg btn-add p-0 rounded-circle">＋</button>
                        Link Account
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-7 col-xl-8">
</div>
@endsection

