<template>
    <div class="card">
        <!-- Alert modal -->
        <div id="alertModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="alertModalTitle" aria-hidden="true" @keyup.enter="hideModal('#alertModal')">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header" :class="[alertModal.borderType]">
                        <h5 class="modal-title" id="alertModalTitle">{{ alertModal.title }}</h5>

                        <button type="button" class="close" aria-label="Close" @click="hideModal('#alertModal')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p class="card-text">
                            {{ alertModal.message }}
                        </p>
                    </div>

                    <div class="modal-footer" :class="[alertModal.borderType]">
                        <button type="button" class="btn btn-primary" @click="hideModal('#alertModal')">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="card-header bg-transparent">
            {{ account.name }} <small class="text-muted">&#64;{{ account.nickname }}</small> - {{ providerName[account.provider_name] }}
        </h5>

        <div class="card-body">
            <div class="card border-light">
                <h5 class="card-header bg-transparent border-primary">Notification</h5>

                <div class="list-group list-group-flush">
                    <form class="list-group-item d-sm-flex d-lg-block d-xl-flex">
                        <input class="m-1 p-1 w-100 text-body" v-model="message" placeholder="Notification Test. (customizable)">
                        <button class="btn btn-secondary ml-auto align-self-center" type="submit" @click.prevent="testNotify">
                            Post
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-light">
                <h5 class="card-header bg-transparent border-danger">Danger Zone</h5>

                <div class="list-group list-group-flush">
                    <div class="list-group-item d-sm-flex d-lg-block d-xl-flex">
                        <p class="m-1 p-1 w-100">
                            This account unlink from you.<br/>
                            However, you can't unlink this if it is the only linked account, if no email and password are set.
                        </p>
                        <button class="btn btn-danger ml-auto align-self-center" type="button" @click.prevent="showModal('#unlinkModal')" :disabled="!isSafeUnlink">
                            Unlink this account
                        </button>
                    </div>
                </div>

                <!-- Unlink modal -->
                <div id="unlinkModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="unlinkModalTitle" aria-hidden="true" @keyup.enter="hideModal('#unlinkModal')">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header border-danger">
                                <h5 class="modal-title" id="unlinkModalTitle">Unlink this account</h5>

                                <button type="button" class="close" aria-label="Close" @click="hideModal('#unlinkModal')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <p class="card-text">
                                    This account (<strong>&#64;{{ account.nickname }} - {{ providerName[account.provider_name] }}</strong>) will be deauthenticated.<br/>
                                    Are you okay?
                                </p>
                            </div>

                            <div class="modal-footer border-danger">
                                <button type="button" class="btn btn-secondary" @click="hideModal('#unlinkModal')">Cancel</button>
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
            csrfToken: String,
            endpoints: Object,
            isSafeUnlink: {
                type: Boolean,
                default: false,
            },
            providerName: {
                type: Object,
                default: {},
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
        data() {
            return {
                params: {
                    id: this.account.provider_id,
                    _token: this.csrfToken
                },
                message: null,
                alertModal: {
                    title: null,
                    message: null,
                    borderType: null
                }
            }
        },
        methods: {
            showModal(modalName) {
                $(modalName).modal('show')
            },
            hideModal(modalName) {
                $(modalName).modal('hide')
            },
            resultAlert([resData, isSuccess]) {
                let consoleText = resData.status+': '+resData.message
                if (isSuccess) {
                    console.log(consoleText)
                    this.alertModal.borderType = "border-success"
                } else {
                    console.error(consoleText)
                    this.alertModal.borderType = "border-danger"
                }

                this.alertModal.title = resData.status
                this.alertModal.message = resData.message
                this.showModal('#alertModal')
                return [resData, isSuccess]
            },
            testNotify() {
                let formData = this.params
                formData.message = this.message || "Notification Test."

                axios.post(this.endpoints[this.account.provider_name].notify, formData)
                    .then((res) => {
                        return [res.data, true]
                    })
                    .catch((e) => {
                        return [e.response.data, false]
                    })
                    .then(([resData, isSuccess]) => {
                        if (isSuccess) {
                            resData.status = "Success"
                        }
                        return [resData, isSuccess]
                    }).then(this.resultAlert)
            },
            unlinkAccount() {
                let formData = this.params

                axios.delete(this.endpoints[this.account.provider_name].unlink, { params: formData })
                    .then((res) => {
                        if (this.accounts[this.account.provider_name].length < 2) {
                            Vue.delete(this.accounts, this.account.provider_name)
                        } else {
                            Vue.delete(this.accounts[this.account.provider_name], this.accountIndex)
                        }
                        this.$emit('update:accounts', accounts)
                        return [res.data, true]
                    })
                    .catch((e) => {
                        return [e.response.data, false]
                    })
                    .then(([resData, isSuccess]) => {
                        if (isSuccess) {
                            resData.status = "Unlinked"
                            resData.message = this.providerName[this.account.provider_name]+" / "+this.account.name+"."
                        }
                        this.hideModal('#unlinkModal')
                        return [resData, isSuccess]
                    }).then(this.resultAlert)
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
