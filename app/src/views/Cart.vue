<template>
    <div class="iktickets-cart-page">
        <h2 class="the-subtitle">{{ $t("ticketing.cart") }}</h2>
        <Cart />
        <div class="total">
            <p class="the-subtitle-iktickets-medium">{{ $t("ticketing.total") }}</p>
            <p class="the-subtitle-iktickets-medium amount">{{ $t("ticketing.currency.CHF") }} {{ total }}</p>
        </div>
        <div class="manage-cart">
            <button v-if="!total" class="btn btn-back-cart" @click="backCart">{{ $t("ticketing.back_to_ticket") }}</button>
            <button v-if="cartStore.getCartSize"  class="btn btn-empty-cart" @click="clearCart">{{ $t("ticketing.clear_to_cart") }}</button>
            <button v-if="cartStore.getCartSize && cartStore.getTotal"  class="btn btn-buy-cart" @click="buyCart">{{ $t("ticketing.buy_cart") }}</button>
            <button v-if="cartStore.getCartSize && !cartStore.getTotal"  class="btn btn-buy-cart" @click="buyCart">{{ $t("ticketing.checkout.valid_order") }}</button>
        </div>
    </div>
</template>
<script setup>
import { useCartStore } from '@/stores/cart';
import { useUserStore } from '@/stores/user';
import { ref, watch } from 'vue';
import router from '@/router/routes';
import Cart from '@/components/cart/Cart.vue';

const cartStore = useCartStore();
const userStore = useUserStore();
const total = ref(cartStore.getTotal);

const resetZones = () => {
    const zones = document.querySelectorAll('.zones-prices');
    const cartContent = document.querySelectorAll('.cart-items');
    if (zones) {
        zones.forEach(zone => {
            zone.setAttribute('data-amount', 0);
            zone.querySelector('.items-counter').value = 0;
        });

        if (cartContent) {
            cartContent.forEach(content => {
                content.remove();
            });
        }
    }
};

const buyCart = () => {
    // redirect to checkout page after saving cart
    if (userStore.isLogged()) {
        cartStore.buyCart();
        router.push({ name: 'checkout' });
    } else {
        router.push({ name: 'login' });
    }
};

const clearCart = () => {
    cartStore.cancelOrder();
    cartStore.clearCart();
    // reset zones
    resetZones();
};

const backCart = () => {
    // redirect to ticket page
    router.push({ name: 'billetterie' });
};

// watch cart amount to update total
watch(() => cartStore.getTotal, () => {
    total.value = cartStore.getTotal;
});

</script>

<style lang="scss" scoped>
.iktickets-cart-page {
    .total {
        display: flex;
        justify-content:flex-end;
        margin-top: var(--iktickets-sp-md);

        .amount {
            margin-left: var(--iktickets-sp-xl) !important;
        }
    }

    .manage-cart {
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

            &-back-cart,
            &-empty-cart {
                color: var(--iktickets-color-black);
                border-color: var(--iktickets-color-black);
            }
        }
    }
}
</style>