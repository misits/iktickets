<template>
    <div class="iktickets-checkout-page">
        <h2 class="the-subtitle--iktickets-sp-md" v-if="cartSold">
            {{ $t("ticketing.checkout.title") }}
        </h2>
        <h2 v-else>{{ $t("ticketing.checkout.description_free") }}</h2>
        <p class="description" v-if="cartSold">
        {{ $t("ticketing.checkout.description") }}
        </p>
        <PaiementForm v-if="cartSold"/>
        <div class="free-order-recap" v-else>
            <Cart />
            <button class="iktickets-btn valid-free-order" @click="valid">
                {{ $t("ticketing.checkout.valid_order") }}
            </button>
        </div>
    </div>
</template>
<script setup>
import PaiementForm from '@/components/checkout/PaiementForm.vue';
import Cart from '@/components/cart/Cart.vue';
import { useCartStore } from '@/stores/cart';

const cartStore = useCartStore();
const cartSold = cartStore.getTotal;

const valid = () => {
    cartStore.validFreeOrder();
}
</script>

<style lang="scss" scoped>
.free-order-recap {
    margin-top: var(--iktickets-sp-md);

    .btn-counter {
        display: none !important;
    }
}

.iktickets-btn {
    &.valid-free-order {
        margin-top: var(--iktickets-sp-sm);
    }
}
</style>