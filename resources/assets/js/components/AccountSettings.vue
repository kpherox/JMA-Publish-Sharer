<template>
    <div class="card card-default">
        <div class="card-body">
            <h5 class="card-title">{{ name }} <small class="text-muted">&#64;{{ nickname }}</small> - {{ provider }}</h5>

            I'm an example component!
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'provider',
            'id',
            'name',
            'nickname',
            'token',
        ],
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
