<template>
    <div class="iktickets-order-page">
        <h2 class="the-subtitle">{{ $t("ticketing.how_many") }}</h2>
        <p class="the-subtitle-iktickets-medium">{{ $t("ticketing.selected_date") }}</p>
        <p class="the-event-date">{{ moment(event.date).format('D.M.YYYY, H[h]mm') }}</p>
        <Zones/>
        <div class="total">
            <p class="the-subtitle-iktickets-medium">{{ $t("ticketing.total") }}</p>
            <p class="the-subtitle-iktickets-medium amount">{{ $t("ticketing.currency.CHF") }} {{ total }}</p>
        </div>
        <div class="add-to-cart" v-if="cartStore.getCartSize">
            <button class="btn btn-save-cart" @click="saveCart">{{ $t("ticketing.add_to_cart") }}</button>
        </div>  
    </div>
</template>
<script setup>
import { useEventsStore } from '@/stores/events';
import { useCartStore } from '@/stores/cart';
import { useRoute } from 'vue-router';
import { ref, watch } from 'vue';
import moment from 'moment';
import Zones from '@/components/order/Zones.vue';
import router from '@/router/routes';

const eventsStore = useEventsStore();
const cartStore = useCartStore();
const route = useRoute();
const slug = route.params.slug;
const eventId = route.params.event_id;
const event = ref(eventsStore.getEventById(slug, eventId));
const total = ref(cartStore.getAmount);

const resetZones = () => {
    const zones = document.querySelectorAll('.zones-prices');
    zones.forEach(zone => {
        zone.setAttribute('data-amount', 0);
        zone.querySelector('.items-counter').value = 0;
    });
};

const saveCart = () => {
    cartStore.saveCart();
    resetZones();
    // redirect to cart page after saving cart
    router.push({ name: 'cart' });
};

// watch cart amount to update total
watch(() => cartStore.getAmount, () => {
    total.value = cartStore.getAmount;
});

// watch route params to update event
watch(() => route.params, () => {
    event.value = eventsStore.getEventById(slug, eventId);
}, { immediate: true });
</script>

<style lang="scss" scoped>
.iktickets-order-page {
    .the-event-date {
        font-size: var(--iktickets-font-size-lg);
        margin: 0 0 var(--iktickets-sp-md);
    }

    .total {
        display: flex;
        justify-content:flex-end;
        margin-top: var(--iktickets-sp-md);

        .amount {
            margin-left: var(--iktickets-sp-xl) !important;
        }
    }

    .add-to-cart {
        display: flex;
        justify-content:flex-end;
        width: 100%;

        .btn {
            margin-top: 65px;
            background-color: inherit;
            border-top: 0;
            border-left: 0;
            border-right: 0;
            border-bottom: 1px solid var(--iktickets-color-main);
            color: var(--iktickets-color-main);
            text-transform: uppercase;
            font-size: var(--iktickets-font-size-sm);
            padding: 0 0 5px 0;
            cursor: pointer;
            margin-left: var(--iktickets-sp-md);

            &-empty-cart {
                color: var(--iktickets-color-black);
                border-color: var(--iktickets-color-black);
            }
        }
    }
}
</style>