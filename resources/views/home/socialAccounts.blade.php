@extends ('layouts.home')
@section ('title', 'Social Accounts')

@section ('page-content')
<div id="socialAccounts" class="card">
    <h5 class="card-header">Acounts List</h5>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3 text-sm-right mt-sm-1 mb-2">Twitter</dt>
                <dd class="col-sm-9 ml-3 ml-sm-0 mb-2">
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
@endsection

