<template>
    <div v-if="number" class="zones-prices" :data-amount="amount">
        <p class="category">{{ item.category.name_languages[locale] }}</p>
        <div class="counter">
            <button class="btn-counter" @click="decrement">-</button>
            <input class="items-counter" type="number" v-model.number="number" :max="freeSeats" readonly/>
            <button class="btn-counter" @click="increment">+</button>
        </div>
    </div>
</template>
  
<script>
import { ref, watch } from 'vue';
import { useCartStore } from '@/stores/cart';
import { useI18n } from 'vue-i18n';

export default {
    props: {
        item: {
            type: Object,
            default: () => {},
        },
    },
    setup(props) {
        // Get current i18n locale
        const locale = useI18n().locale.value;
        const cartStore = useCartStore();
        const number = ref(props.item.quantity);
        const amount = ref(0);
        const freeSeats = ref(props.item.category.free_seats);

        function increment() {
            if (number.value === freeSeats.value) {
                return;
            }
            if (number.value < freeSeats.value) {
                number.value++;
            }
        }

        function decrement() {
            if (number.value === 0) {
                return;
            }
            if (number.value > 0) {
                number.value--;
            }
        }

        // watch input value if set manually from keyboard
        watch(number, (newValue, oldValue) => {
            // if number is greater than free seats, set it to free seats
            if (newValue > freeSeats.value) {
                number.value = freeSeats.value;
            }
            // if number is less than 0, set it to 0
            if (newValue < 0) {
                number.value = 0;
            }
            // Calculate the difference in number of tickets
            const ticketsDifference = newValue - oldValue;

            // Calculate the monetary difference using CHF price
            const difference = ticketsDifference * props.item.category.price['CHF'];

            // Update the local amount
            amount.value += difference;

            // Update the cart's total amount
            let newAmount = cartStore.getAmount + difference;
            cartStore.setAmount(newAmount);

            // Update the cart's items accordingly
            if (ticketsDifference > 0) {
                // If the difference is positive, items were added.
                cartStore.addToCart(props.item.event, props.item.category, ticketsDifference);
            } else if (ticketsDifference < 0) {
                // If the difference is negative, items were removed.
                cartStore.removeFromCart(props.item.event, props.item.category, -ticketsDifference); // Take absolute value of ticketsDifference
            }
            // save cart to local storage
            cartStore.saveCart();
        });

        return { number, increment, decrement, amount, locale, freeSeats };
    },
};
</script>

<style lang="scss" scoped>
.zones-prices {
    display: flex;
    justify-content: space-between;
    background-color: var(--iktickets-calendar-border-color);
    min-height: 65px;
    padding: var(--iktickets-sp-sm);
    margin-bottom: var(--iktickets-sp-xs);
    border-radius: var(--iktickets-border-radius);

    .category {
        display: flex;
        align-items: center;
        margin: 0;
        font-weight: var(--font-weight-iktickets-medium);
    }

    .counter {
        display: flex;
        align-items: center;

        .btn-counter {
            height: 100%;
            width: 25px;
            border: none;
            background: none;
            outline: none;
            cursor: pointer;
            font-size: var(--iktickets-font-size-lg);
        }

        .items-counter {
            width: 40px;
            height: 40px;
            text-align: center;
            background-color: white;
            border: none;
        }
    }

    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
}
</style>