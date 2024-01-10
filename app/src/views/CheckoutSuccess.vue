<template>
    <div class="iktickets-checkout iktickets-checkout-status-page--success">
        <div class="checkout-success">
            <h2 class="checkout-success__title">{{ $t("ticketing.checkout.success_title") }}</h2>
            <p class="checkout-success__text">{{ $t("ticketing.checkout.success_description") }}</p>
            <p class="checkout-success__text">{{ $t("ticketing.checkout.success_description2") }}</p>
            <router-link :to="{name: 'home'}" class="iktickets-btn iktickets-btn--back">{{ $t("ticketing.checkout.back_button") }}</router-link>
        </div>

    </div>
</template>

<script setup>

import { useCartStore } from '@/stores/cart';
import { useUserStore } from '@/stores/user';

const cartStore = useCartStore();
const userStore = useUserStore();

cartStore.clearCart();
</script>

<style lang="scss" scoped>
.iktickets-btn--back {
    margin-top: var(--iktickets-sp-xs);
    display: inline-block;
}
</style>