<template>
    <a :href="slug">
        <slot/>
    </a>
</template>

<script>
export default {
    props: {
        slug: {
            type: String,
            required: true
        }
    },
    setup(props) {
        // current hostname
        const hostname = window.location.hostname;

        return {
            slug: props.slug
        }
    }
}
</script>