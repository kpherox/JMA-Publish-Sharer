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
                <script>accounts.{{ $provider }} = @json($accounts->toArray())</script>
                <h5 class="card-header bg-transparent border-info">{{ $providerName[$provider] }}</h5>
                <transition-group name="fade" tag="div" class="list-group list-group-flush">
                    <a v-for="(account, index) in accounts.{{ $provider }}" class="list-group-item list-group-item-action" :key="account" :href="'#'+account.provider_name+'-'+account.nickname" :class="{'disabled' : isShowing}"
                       v-on:click.prevent="showAccountSettings('{{ route('index') }}', account, index)">
                        <img class="rounded-circle social-avatar" :src="account.avatar" alt="{{ $provider }} @{{ account.name }} Icon" />
                        <div class="d-inline-block align-middle">
                            <p class="mb-0">@{{ account.name }}</p>
                            <p class="mb-0 text-muted">&#64;@{{ account.nickname }}</p>
                        </div>
                    </a>
                    <a class="list-group-item list-group-item-action" :key="account" href="{{ route($provider.'.linktouser') }}">
                        <button type="button" class="btn btn-secondary btn-lg btn-add p-0 rounded-circle">＋</button>
                        <p class="d-inline-block align-middle mb-0">
                            Link Account
                        </p>
                    </a>
                </transition-group>
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
            :is-safe-unlink="{{ $isSafeUnlink }}"
            :accounts="accounts"
            v-on:update:accounts="updateList($event)"
            :account-index="accountIndex"
            :account="account">
        </account-settings>
    </transition>
    <script>
    mix = {
        data: { endpoints: { unlink: '/' }, isDisplay: false, isShowing: false, account: {}, accountIndex: 0 },
        methods: {
            showAccountSettings(unlinkEndpoint, account, accountIndex) {
                if (this.isShowing) return;

                let delay = this.isDisplay ? 500 : 0;

                this.isShowing = true;
                this.isDisplay = false;

                setTimeout(() => {
                    this.endpoints.unlink = unlinkEndpoint;
                    this.account = account;
                    this.accountIndex = accountIndex;

                    this.isDisplay = true;
                }, delay);

                setTimeout(() => {
                    console.log('showed '+account.name+'\'s settings');
                    this.isShowing = false;
                }, delay + 500);
            },
            updateList(e) {
                this.accounts = e
                this.$forceUpdate()
                this.isDisplay = false;
            }
        }
    }
    </script>
</div>
@endsection

