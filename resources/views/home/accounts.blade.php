@extends ('layouts.home')
@section ('title', 'Social Accounts')

@section ('page-content')
<div class="col-lg-5 col-xl-4">
    <div id="account-lists" class="card">
        <h5 class="card-header">Account Lists</h5>
        <div class="card-body">
            <script>
            Object.assign(mix.data, {
                accounts: {},
            });
            </script>
            @foreach ($socialAccounts as $provider => $accounts)
            <div class="card border-0">
                @if ($accounts->count() > 0)
                <script>
                Object.assign(mix.data.accounts, {
                    {{ $provider }}: @json ($accounts),
                });
                </script>
                @endif
                <h5 class="card-header bg-transparent border-info">{{ $providersName[$provider] }}</h5>
                <div class="list-group list-group-flush">
                    <transition-group name="fade" tag="div" class="account-list">
                        <a v-for="(account, index) in accounts.{{ $provider }}" :key="account"
                           class="d-flex list-group-item list-group-item-action"
                           :class="{'disabled' : isShowing, 'active': account === selectedAccount && ! isShowing}"
                           :href="'#'+account.provider_name+'-'+(account.nickname || account.name || index)"
                           v-on:click.prevent="showAccountSettings(account, index)">
                            <img class="rounded-circle social-avatar mr-2" :src="account.avatar" alt="{{ $provider }} @{{ account.name }} Icon" />
                            <div class="align-self-center">
                                <p class="mb-0">@{{ account.name }}</p>
                                <p class="mb-0" :class="account === selectedAccount && ! isShowing ? 'text-light' : 'text-muted'" v-if='account.nickname'>&#64;@{{ account.nickname }}</p>
                            </div>
                        </a>
                    </transition-group>
                    <a class="d-flex list-group-item list-group-item-action" href="{{ route($provider.'.linktouser') }}">
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
            :provider-name="{{ $providersName }}"
            :endpoints="{{ $endpoints }}"
            :is-safe-unlink="isSafeUnlink"
            :accounts="accounts"
            v-on:update:accounts="updateList($event)"
            :account-index="accountIndex"
            :account="selectedAccount">
        </account-settings>
    </transition>
</div>

<script>
Object.assign(mix.data, {
    isSafeUnlink: false,
    existsEmail: @json ($existsEmail),
    isDisplay: false,
    isShowing: false,
    selectedAccount: {},
    accountIndex: -1,
});
Object.assign(mix.methods, {
    showAccountSettings(account, accountIndex) {
        if (this.isShowing || account === this.selectedAccount) {
            return 0
        }

        let delay = this.isDisplay ? 500 : 0

        this.selectedAccount = {}
        this.isShowing = true
        this.isDisplay = false

        setTimeout(() => {
            this.checkSafeUnlink()
            this.selectedAccount = account
            this.isDisplay = true
        }, delay)

        setTimeout(() => {
            console.log('showed '+account.name+'\'s settings')
            this.accountIndex = accountIndex
            this.isShowing = false
        }, delay + 500)
    },
    checkSafeUnlink() {
        let count = 0

        if (this.existsEmail) {
            return this.isSafeUnlink = true
        }

        for (let provider of Object.keys(this.accounts)) {
            count += this.accounts[provider].length

            if (count > 1) {
                return this.isSafeUnlink = true
            }
        }

        return this.isSafeUnlink = false
    },
    updateList(e) {
        this.accounts = e
        this.$forceUpdate()
        this.isDisplay = false
    }
});
</script>
@endsection

