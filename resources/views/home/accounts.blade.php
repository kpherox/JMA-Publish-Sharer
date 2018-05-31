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
                    <a href="#{{ $account->provider_name }}-{{ $account->nickname }}" class="list-group-item list-group-item-action" :class="{disabled: isShowing}"
                       v-on:click.prevent="showAccountSettings('{{ $provider }}', '{{ $account->provider_id }}', '{{ $account->name }}', '{{ $account->nickname }}')">
                        <img class="rounded-circle social-avatar" src="{{ $account->avatar }}" alt="{{ $provider }} {{ $account->name }} Icon" />
                        <div class="d-inline-block align-middle">
                            <p class="mb-0">{{ $account->name }}</p>
                            <p class="mb-0 text-muted">&#64;{{ $account->nickname }}</p>
                        </div>
                    </a>
                    @endforeach
                    <a class="list-group-item list-group-item-action" href="{{ route($account->provider_name.'.linktouser') }}">
                        <button type="button" class="btn btn-secondary btn-lg btn-add p-0 rounded-circle">＋</button>
                        <p class="d-inline-block align-middle mb-0">
                            Link Account
                        </p>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<div class="col-lg-7 col-xl-8">
    <transition name="fade">
        <account-settings v-if="isDisplay"
            csrf-token="{{ csrf_token() }}"
            :provider="providerName"
            :id="providerId"
            :name="accountName"
            :nickname="accountNickname"
            :endpoints="{{ $endpoints }}"
            :is-safe-unlink="{{ $isSafeUnlink }}">
        </account-settings>
    </transition>
    <script>
    mix = {
        data: {
            providerName: '',
            providerId: '',
            accountName: '',
            accountNickname: '',
            isDisplay: false,
            isShowing: false
        },
        methods: {
            showAccountSettings(provider, id, name, nickname) {
                if (this.isShowing) return;

                let delay = this.isDisplay ? 500 : 0;

                this.isShowing = true;
                this.isDisplay = false;

                setTimeout(() => {
                    this.providerName = provider;
                    this.providerId = id;
                    this.accountName = name;
                    this.accountNickname = nickname;

                    this.isDisplay = true;
                }, delay);

                setTimeout(() => {
                    console.log('showed '+name+'\'s settings');
                    this.isShowing = false;
                }, delay + 500);
            }
        }
    }
    </script>
</div>
@endsection

