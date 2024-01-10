<template>
    <div class="iktickets-billetterie-page">
        <section class="cards-grid">
            <Event v-for="event in wpdataStore.getPosts" :key="event.id" :event="event" />
        </section>
    </div>
</template>
<script setup>
import Event from '@/components/ui/cards/Event.vue';
import { useWpdataStore } from '@/stores/wpdata';

const wpdataStore = useWpdataStore();

</script>

<style lang="scss" scoped>
.iktickets-billetterie-page {
    .cards-grid {
        display: grid;
        grid-template-columns: 1fr;
        grid-gap: var(--iktickets-sp-md);

        @media screen and (min-width: 768px) {
            grid-template-columns: repeat(3, minmax(300px,1fr));
        }

        @media screen and (min-width: 1024px) {
            grid-template-columns: repeat(4, minmax(300px,1fr));
        }

        @media screen and (min-width: 1280px) {
            grid-template-columns: repeat(5, minmax(300px,1fr));
        }
    }
}
</style>