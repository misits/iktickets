<template>
  <div v-if="dataready">
    <div v-for="zone in zones" :key="zone.zone_id" :class="'event-' + eventId">
      <div v-for="category in zone.categories" :key="category.tariff_id">
        <AddToCart :category="category" :freeSeats="category.free_seats" :price="category.amount" :eventID="eventId"/>
      </div>
    </div>
  </div>
  <p v-else>{{ $t("ticketing.loading") }}</p>
</template>  

<script setup>
import { useRoute } from 'vue-router';
import { ref, watch } from 'vue';
import axios from 'axios';
import AddToCart from './AddToCart.vue';

const route = useRoute();
const eventId = route.params.event_id;
const zones = ref([]);
const dataready = ref(false);

const fetchZone = () => {
  axios.get(`/event/${eventId}/zones`)
    .then((response) => {
      zones.value = response.data;

      // sort zones by category.price.CHF
      zones.value.forEach((zone) => {
        zone.categories.sort((a, b) => {
          return b.price.CHF - a.price.CHF;
        });

        // 1st = "Plein tarif (dès 16 ans)" / 2nd = "Tarif réduit" / 3rd = "Gratuit (moins de 16 ans)" / 4th = "combiné"
        zone.categories.sort((a, b) => {
          if (a.name === "Plein tarif (dès 16 ans)") {
            return -1;
          } else if (b.name === "Plein tarif (dès 16 ans)") {
            return 1;
          } else if (a.name === "Tarif réduit") {
            return -1;
          } else if (b.name === "Tarif réduit") {
            return 1;
          } else if (a.name === "Gratuit (moins de 16 ans)") {
            return -1;
          } else if (b.name === "Gratuit (moins de 16 ans)") {
            return 1;
          } else if (a.name === "Combiné") {
            return -1;
          } else if (b.name === "Combiné") {
            return 1;
          } else {
            return 0;
          }
        });
      });
      dataready.value = true;
      console.log(zones.value);
    })
    .catch((error) => {
      console.log(error);
    });
};

// watch route params to update event
watch(() => route.params, () => {
  // fetch zones
  fetchZone();
}, { immediate: true });
</script>
