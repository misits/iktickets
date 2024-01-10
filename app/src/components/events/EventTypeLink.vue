<template>
    <router-link :to="{ name: 'events', params: {slug: slug } }">
        <slot/>
    </router-link>
</template>

<script>
export default {
    props: {
        slug: {
            type: String,
            required: true
        }
    }
}
</script>