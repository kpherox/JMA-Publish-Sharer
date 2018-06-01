<template>
    <div class="card">
        <h5 class="card-header bg-transparent">
            {{ account.name }} <small class="text-muted">&#64;{{ account.nickname }}</small> - {{ providerName[account.provider_name] }}
        </h5>

        <div class="card-body">
            <p class="card-text">I'm an example component!</p>

            <div class="card border-light">
                <h5 class="card-header bg-transparent border-danger">Danger Zone</h5>

                <div class="list-group list-group-flush">
                    <div class="list-group-item d-sm-flex d-lg-block d-xl-flex">
                        <p class="mb-0 p-2">
                            This account unlink from you.<br/>
                            However, you can't unlink this if it is the only linked account, if no email and password are set.
                        </p>
                        <button class="btn btn-danger ml-auto align-self-center" type="button" data-toggle="modal" data-target="#unlinkModal" :disabled="!isSafeUnlink">
                            Unlink this account
                        </button>
                    </div>
                </div>

                <!-- Unlink modal -->
                <div id="unlinkModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="unlinkModalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header border-danger">
                                <h5 class="modal-title" id="unlinkModalTitle">Unlink this account</h5>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <p class="mb-0">
                                    This account (<strong>&#64;{{ account.nickname }} - {{ providerName[account.provider_name] }}</strong>) will be deauthenticated.<br/>
                                    Are you okay?
                                </p>
                            </div>

                            <div class="modal-footer border-danger">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" @click="unlinkAccount">OK</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            csrfToken: {
                type: String,
                default: '',
            },
            providerName: {
                type: Object,
                default: {},
            },
            endpoints: {
                type: Object,
                default: {
                    unlink: '',
                },
            },
            isSafeUnlink: {
                type: Boolean,
                default: false,
            },
            accounts: {
                type: Object,
                default: {},
            },
            accountIndex: {
                type: Number,
                default: 0,
            },
            account: {
                type: Object,
                default: {},
            },
        },
        methods: {
            unlinkAccount() {
                console.log("Unlinked "+this.providerName[this.account.provider_name]+" / "+this.account.name)
                if (this.accounts[this.account.provider_name].length < 2) {
                    Vue.delete(this.accounts, this.account.provider_name)
                } else {
                    Vue.delete(this.accounts[this.account.provider_name], this.accountIndex)
                }
                this.$emit('update:accounts', accounts)
                jQuery(() => $('#unlinkModal').modal('hide'))
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
