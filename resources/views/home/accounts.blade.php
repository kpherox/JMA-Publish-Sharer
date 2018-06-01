@extends ('layouts.home')
@section ('title', 'Social Accounts')

@section ('page-content')
<div class="col-lg-5 col-xl-4">
    <div id="account-lists" class="card">
        <h5 class="card-header">Account Lists</h5>
        <div class="card-body">
            <script>let accounts = {}</script>
            @foreach ($socialAccounts as $provider => $accounts)
            <div class="card border-0">
                @if ($accounts->count() > 0)
                <script>accounts.{{ $provider }} = @json($accounts->toArray())</script>
                @endif
                <h5 class="card-header bg-transparent border-info">{{ $providerName[$provider] }}</h5>
                <div class="list-group list-group-flush">
                    <transition-group name="fade" class="account-list">
                        <a class="d-flex list-group-item list-group-item-action" :class="{'disabled' : isShowing, 'active': accountIndex === index }"
                           v-for="(account, index) in accounts.{{ $provider }}" :key="account"
                           :href="'#'+account.provider_name+'-'+account.nickname"
                           v-on:click.prevent="showAccountSettings('{{ route('index') }}', account, index)">
                            <img class="rounded-circle social-avatar mr-2" :src="account.avatar" alt="{{ $provider }} @{{ account.name }} Icon" />
                            <div class="align-self-center">
                                <p class="mb-0">@{{ account.name }}</p>
                                <p class="mb-0" :class="{'text-light': accountIndex === index, 'text-muted': accountIndex !== index}">&#64;@{{ account.nickname }}</p>
                            </div>
                        </a>
                    </transition-group>
                    <a class="d-flex list-group-item list-group-item-action" :class="{'disabled' : isShowing}" href="{{ route($provider.'.linktouser') }}">
                        <button type="button" class="btn btn-secondary btn-lg btn-add p-0 rounded-circle mr-2">＋</button>
                        <p class="align-self-center mb-1">
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
            :provider-name="{{ $providerName }}"
            :endpoints="endpoints"
            :is-safe-unlink="isSafeUnlink"
            :accounts="accounts"
            v-on:update:accounts="updateList($event)"
            :account-index="accountIndex"
            :account="account">
        </account-settings>
    </transition>
    <script>
    mix = {
        data: { endpoints: { unlink: '/' }, isSafeUnlink: false, existsEmail: {{ $existsEmail }}, isDisplay: false, isShowing: false, accounts: accounts, account: {}, accountIndex: -1 },
        methods: {
            showAccountSettings(unlinkEndpoint, account, accountIndex) {
                if (this.isShowing) return;

                let delay = this.isDisplay ? 500 : 0;

                this.isShowing = true;
                this.isDisplay = false;

                setTimeout(() => {
                    this.checkSafeUnlink();
                    this.endpoints.unlink = unlinkEndpoint;
                    this.account = account;

                    this.isDisplay = true;
                }, delay);

                setTimeout(() => {
                    console.log('showed '+account.name+'\'s settings');
                    this.accountIndex = accountIndex;
                    this.isShowing = false;
                }, delay + 500);
            },
            checkSafeUnlink() {
                let count = 0;

                if (this.existsEmail) {
                    return this.isSafeUnlink = true;
                }

                for (let provider of Object.keys(this.accounts)) {
                    count += this.accounts[provider].length;

                    if (count > 1) {
                        return this.isSafeUnlink = true;
                    }
                }

                return this.isSafeUnlink = false;
            },
            updateList(e) {
                this.accounts = e;
                this.$forceUpdate();
                this.isDisplay = false;
            }
        }
    }
    </script>
</div>
@endsection

