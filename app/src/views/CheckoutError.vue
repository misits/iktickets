<template>
    <div class="iktickets-checkout-status-page iktickets-checkout-status-page--error">
        <div class="checkout-error">
            <h2 class="checkout-error__title">{{ $t("ticketing.checkout.error_title") }}</h2>
            <p class="checkout-error__text">{{ $t("ticketing.checkout.error_description") }}</p>
            <p class="checkout-error__text">{{ $t("ticketing.checkout.error_description2") }}</p>
            <router-link :to="{name: 'home'}" class="iktickets-btn iktickets-btn--back">{{ $t("ticketing.checkout.back_button") }}</router-link>
        </div>
    </div>
</template>

<script setup>
import { useCartStore } from '@/stores/cart';
import { useUserStore } from '@/stores/user';

const cartStore = useCartStore();
const userStore = useUserStore();

cartStore.cancelOrder();
cartStore.clearCart();
</script>

<style lang="scss" scoped>
.iktickets-btn--back {
    margin-top: var(--iktickets-sp-xs);
    display: inline-block;
}


</style>