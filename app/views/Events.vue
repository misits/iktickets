<template>
    <div class="iktickets-events-page">
        <h2 class="the-subtitle">{{ $t("ticketing.select_date") }}</h2>
        <Calendar />
    </div>
</template>

<script setup>
import Calendar from '../components/events/Calendar.vue';
</script>

<style lang="scss" scoped>

</style>