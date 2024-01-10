<template>
    <div class="calendar-container">
        <div v-if="events">
            <MonthlyCalendar :events="events"/>
        </div>
        <div v-else>
            <p>{{ $t("ticketing.error.event_not_found", {name: slug}) }}</p>
        </div>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { useEventsStore } from '../../stores/events';
import MonthlyCalendar from './MonthlyCalendar.vue';
import { ref, watch } from 'vue';

// get events store
const eventsStore = useEventsStore();

// get current route params
const route = useRoute();
const slug = route.params.slug;
// get event by slug
const events = ref(eventsStore.getEventBySlug(slug));

// watch route params to update event
watch(() => route.params.slug, (slug) => {
     events.value = eventsStore.getEventBySlug(slug);
});
</script>