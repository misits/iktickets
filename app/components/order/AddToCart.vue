<template>
    <div class="zones-prices" :data-amount="amount">
        <div class="zones-prices__details">
            <p class="category">{{ category.name_languages[locale] }}</p>
            <div class="description" v-if="category.description[locale]" v-html="category.description[locale]"></div>
        </div>
        <div class="counter">
            <p class="price" v-if="category.price.CHF">CHF {{ category.price.CHF }}.-</p>
            <p class="price" v-else>{{ $t("ticketing.free") }}</p>
            <button class="btn-counter btn-counter--minus" @click="decrement">&ndash;</button>
            <input :class="'items-counter items-counter-' + category.name_languages[locale].slugify()" type="number"
                v-model.number="number" min="0" :max="freeSeats" @input="validateNumber"/>
            <button class="btn-counter btn-counter--plus" @click="increment">&#43;</button>
        </div>
    </div>
</template>
  
<script>
import { ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useCartStore } from '../../stores/cart';
import { useEventsStore } from '../../stores/events';
import { useI18n } from 'vue-i18n';

export default {
    props: {
        eventID: {
            type: String,
            default: 0,
        },
        category: {
            type: Object,
            default: () => { },
        },
        freeSeats: {
            type: Number,
            default: 0,
        },
        price: {
            type: Number,
            default: 0,
        },
    },
    setup(props) {
        // Get current i18n locale
        const { t } = useI18n();
        const locale = useI18n().locale.value;
        const route = useRoute();
        const slug = route.params.slug;
        const eventId = route.params.event_id;
        const cartStore = useCartStore();
        const eventsStore = useEventsStore();
        const number = ref(0);
        const amount = ref(0);
        const event = ref(eventsStore.getEventById(slug, eventId));
        const reserved = ref(cartStore.eventCount(props.eventID));
        const maxAvailable = ref(props.freeSeats - reserved.value);

        const maxAvailableMessage = () => {
            const eventContainer = document.querySelector(`.event-${eventId}`);
            eventContainer.setAttribute('data-max-available', maxAvailable.value);
            const cartBtn = document.querySelector('.btn-save-cart');
            if (maxAvailable.value < 0) {
                if (cartBtn && !cartBtn.classList.contains('disabled')) {
                    cartBtn.classList.add('disabled');
                }
                // check if message already exists
                const message = document.querySelector('.max-available-message');
                if (!message) {
                    // add message under eventContainer
                    const message = document.createElement('p');
                    message.classList.add('max-available-message');
                    message.innerHTML = t(`ticketing.max_available`, { max: props.freeSeats, current: reserved.value });
                    eventContainer.prepend(message);
                }
            } else {
                if (cartBtn && cartBtn.classList.contains('disabled')) {
                    cartBtn.classList.remove('disabled');
                }
                // remove message under eventContainer
                const message = document.querySelector('.max-available-message');
                if (message) {
                    message.remove();
                }
            }
        };

        function increment() {
            if (number.value === props.freeSeats) {
                return;
            }

            if (number.value < props.freeSeats && reserved.value < props.freeSeats) {
                number.value++;
            }
        }

        function decrement() {
            if (number.value === 0) {
                return;
            }
            if (number.value > 0 && reserved.value > 0) {
                number.value--;
            }
        }

        function validateNumber() {
            // Use Math.round to convert decimal to nearest integer
            number.value = Math.round(number.value);
        }

        // watch input value if set manually from keyboard
        watch(number, (newValue, oldValue) => {
            // if number is greater than free seats, set it to free seats
            if (newValue > props.freeSeats) {
                number.value = props.freeSeats;
            }

            // if number is less than 0, set it to 0
            if (newValue < 0) {
                number.value = 0;
            }
            // Calculate the difference in number of tickets
            const ticketsDifference = newValue - oldValue;

            // Calculate the monetary difference
            const difference = ticketsDifference * props.price;

            // Update the local amount
            amount.value += difference;

            // Update the cart's total amount
            let newAmount = cartStore.getAmount + difference;
            cartStore.setAmount(newAmount);

            // Update the cart's items accordingly
            if (ticketsDifference > 0 && reserved.value < props.freeSeats) {
                cartStore.addToCart(event.value, props.category, ticketsDifference);
            } else {
                cartStore.removeFromCart(event.value, props.category, -ticketsDifference);
            }
            reserved.value = cartStore.eventCount(props.eventID);
            maxAvailable.value = props.freeSeats - reserved.value;
            maxAvailableMessage();
        });


        return { number, increment, decrement, validateNumber, amount, locale, reserved, maxAvailable };
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

    @media screen and (max-width: 768px) {
        flex-direction: column;
    }

    &__details {
        display: flex;
        flex-direction: column;
        justify-content: center;

        .info-icon {
            cursor: pointer;
        }

        .description {
            font-size: var(--iktickets-font-size-sm);
            line-height: 1.5;
            max-width: 768px;

            p {
                margin: 0;
            }

            a {
                color: var(--iktickets-color-main);
            }
        }
    }

    .category {
        margin: 0;
        font-weight: var(--font-weight-iktickets-medium);
    }

    .counter {
        display: flex;
        align-items: center;

        @media screen and (max-width: 768px) {
            margin-top: var(--iktickets-sp-sm);
        }

        .price {
            margin-top: 5px;
            margin-right: var(--iktickets-sp-sm);
            margin-bottom: 0;
            font-weight: var(--font-weight-iktickets-medium);
            color: var(--iktickets-color-main);
            font-size: var(--iktickets-font-size-sm);
            white-space: nowrap;
        }

        .btn-counter {
            height: 100%;
            border: none;
            background: none;
            outline: none;
            cursor: pointer;
            font-size: var(--iktickets-font-size-lg);
            
            &--minus {
                margin-right: var(--iktickets-sp-xs);
            }

            &--plus {
                margin-left: var(--iktickets-sp-xs);
            }
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