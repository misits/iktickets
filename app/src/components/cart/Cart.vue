<template>
  <div v-for="(group, eventId) in groupedItems" :key="eventId" v-if="items.length" class="cart-items" :class="'event-' + eventId">
    <div class="the-cart-category">
      <p class="the-subtitle-normal">{{ group.event.name }}</p>
      <p class="the-subtitle-iktickets-medium">{{ $t("ticketing.selected_date") }}</p>
      <p class="the-event-date">{{ moment(group.event.date).format('D.M.YYYY, H[h]mm') }}</p>
    </div>
    <CartItem v-for="item in group.items" :key="item.category.tariff_id" :item="item" />
  </div>
</template>

<script setup>
import { useCartStore } from '@/stores/cart';
import CartItem from './CartItem.vue';
import moment from 'moment';
import { watch, ref } from 'vue';

const cartStore = useCartStore();
const items = ref(cartStore.getCart || []);

const groupedItems = ref({});

const groupItems = () => {
  const result = items.value.reduce((acc, item) => {
    if (!acc[item.event.event_id]) {
        acc[item.event.event_id] = {
            event: item.event,
            items: []
        };
    }
    acc[item.event.event_id].items.push(item);
    return acc;
  }, {});

  // Filter out groups with no items
  for (let eventId in result) {
    if (result[eventId].items.length === 0) {
      delete result[eventId];
    }
  }

  // Assign the result to groupedItems.value
  groupedItems.value = result;
};

// Call groupItems initially to populate the groupedItems
groupItems();

// Watch for changes in items
watch(items, groupItems);

</script>

<style lang="scss" scoped>
.cart-items {
  margin-bottom: var(--iktickets-sp-xl);
}

</style>

<style lang="scss" scoped>.the-event-date {
  font-size: var(--iktickets-font-size-lg);
  margin: 0 0 var(--iktickets-sp-md);
}
</style>