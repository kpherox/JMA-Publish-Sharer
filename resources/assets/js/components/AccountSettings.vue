<template>
    <div class="card card-default">
        <div class="card-body">
            <h5 class="card-title">{{ name }} - {{ provider }}</h5>

            I'm an example component!
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'provider',
            'name',
            'id',
            'token',
        ],
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
