<template>
    <div class="card">
        <h5 class="card-header bg-transparent">
            {{ name }} <small class="text-muted">&#64;{{ nickname }}</small> - {{ provider }}
        </h5>

        <div class="card-body">
            <p class="card-text">I'm an example component!</p>

            <div class="card border-light">
                <h5 class="card-header bg-transparent border-danger">Danzer Zone</h5>

                <div class="card-body">
                    <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#unlinkModal">Unlink this account</button>

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
                                    <p>This account will be deauthenticated. Are you okay?</p>
                                </div>

                                <div class="modal-footer border-danger">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger" :disabled="!isSafeUnlink" @click="unlinkAccount">OK</button>
                                </div>
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
            provider: {
                type: String,
                default: '',
            },
            id: {
                type: Number,
                default: '',
            },
            name: {
                type: String,
                default: '',
            },
            nickname: {
                type: String,
                default: '',
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
        },
        methods: {
            unlinkAccount() {
                alert(this.provider+" / "+this.name)
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
