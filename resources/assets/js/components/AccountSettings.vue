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
                            This account (<strong>{{ account.nickname ? '&#64;'+account.nickname : account.name }} - {{ providerName[account.provider_name] }}</strong>) will be deauthenticated.<br/>
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

        <h5 class="card-header bg-transparent">
            {{ account.name }} <small v-if="account.nickname" class="text-muted">&#64;{{ account.nickname }}</small> - {{ providerName[account.provider_name] }}
        </h5>

        <div class="card-body">
            <div class="card border-light">
                <h5 class="card-header bg-transparent border-primary">Notification</h5>

                <div class="list-group list-group-flush list-group-root" v-if="account.can_notify">
                    <div class="list-group-item">
                        <label class="m-0 d-flex">
                            <input class="align-self-center mr-2" v-model="notification.isAllow" type="checkbox" @click.prevent="allowNotification">
                            <p class="mb-0 w-100">
                                Allow notification
                                <br/>
                                <small class="text-muted">When we receive a new entry, Notify from/to this account.</small>
                            </p>
                        </label>
                    </div>
                    <div class="list-group-item">
                        <p class="mb-0">
                            Filter
                        </p>
                    </div>
                    <div v-for="(filter, filterType) in notification.filters" class="list-group list-group-flush">
                        <a :href="'#' + filterType"
                           class="list-group-item text-body d-flex collapseToggle"
                           data-toggle="collapse">
                            <span class="mb-0 w-100">
                                {{filter.name}}
                            </span>
                        </a>
                        <div :id="filterType" class="list-group list-group-flush collapse">
                            <div v-for="(item, itemType) in filter.items"
                                 class="list-group-item">
                                <label class="m-0 d-flex">
                                    <input class="align-self-center mr-2" v-model="item.isAllow" type="checkbox" @click.prevent="changeFilter(filterType, itemType)">
                                    <p class="mb-0 w-100">
                                        {{item.name}}
                                    </p>
                                </label>
                            </div>
                        </div>
                    </div>
                    <form class="list-group-item d-flex">
                        <input class="m-1 p-1 w-100 text-body" v-model="message" placeholder="Notification Test. (customizable)">
                        <button class="btn btn-secondary ml-auto align-self-center" type="submit" @click.prevent="testNotify">
                            Post
                        </button>
                    </form>
                </div>
                <div class="card-body" v-else>
                    <p class="card-text">
                        This account provider can't notification.
                    </p>
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
            </div>
        </div>
    </div>
</template>

<style>
.list-group.list-group-root .list-group {
    margin-top: 1px;
    margin-bottom: 0;
}

.list-group.list-group-root .list-group > .list-group-item {
    border-radius: 0;
}

.list-group.list-group-root .list-group > .list-group-item:first-child {
    border-top: 1px;
}

.list-group.list-group-root > .list-group > .list-group-item {
    padding-left: 2rem;
}

.list-group.list-group-root > .list-group > .list-group > .list-group-item {
    padding-left: 3rem;
}

.collapseToggle {
    z-index: 1;
}

.collapseToggle::before {
    content: "\25b8";
    font-size: 1.5rem;
    margin: -.3rem .5rem 0 3px;
    min-width: 10px;
    max-width: 10px;
    line-height: 1rem;
    display: inline-block;
    align-self: center;
}
.collapseToggle[aria-expanded="true"]::before {
    content: "\25be";
}
</style>

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
            notificationFilters: {
                type: Object,
                default: {},
            }
        },
        data() {
            return {
                params: {
                    id: this.account.provider_id,
                    _token: this.csrfToken,
                },
                message: null,
                notification: {
                    isAllow: false,
                    filters: this.notificationFilters,
                },
                alertModal: {
                    title: null,
                    message: null,
                    borderType: null,
                },
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
            changeFilter(filterType, itemType) {
                let formData = this.params
                formData.type = 'notification'
                formData.key = 'filters->'+filterType+'->'+itemType
                formData.value = !this.notification.filters[filterType].items[itemType].isAllow

                axios.post(this.endpoints[this.account.provider_name].settings, formData)
                    .then((res) => {
                        let resData = res.data
                        this.notification.filters[filterType].items[itemType].isAllow = resData.settings.filters[filterType][itemType] || false
                    })
            },
            allowNotification() {
                let formData = this.params
                formData.type = 'notification'
                formData.key = 'isAllow'
                formData.value = !this.notification.isAllow

                axios.post(this.endpoints[this.account.provider_name].settings, formData)
                    .then((res) => {
                        let resData = res.data
                        this.notification.isAllow = resData.settings.isAllow || false
                    })
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
            },
        },
        created() {
            let formData = this.params
            axios.get(this.endpoints[this.account.provider_name].settings, { params: formData })
                .then((res) => {
                    let resData = res.data
                    this.notification.isAllow = resData.settings.notification.isAllow || false

                    this.notification.filters
                    Object.keys(this.notification.filters).forEach((filterType) => {
                        for (let itemType of Object.keys(this.notification.filters[filterType].items)) {
                            this.notification.filters[filterType].items[itemType].isAllow = resData.settings.notification.filters[filterType][itemType] || false
                        }
                    });
                })
        },
        mounted() {
            console.log('Component mounted.')
        },
    }
</script>
