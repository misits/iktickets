<template>
    <router-link :to="{ name: 'event-order', params: {event_id: event_id } }">
        <slot/>
    </router-link>
</template>

<script>
export default {
    props: {
        event_id: {
            type: String,
            required: true
        }
    }
}
</script>