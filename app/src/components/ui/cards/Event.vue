<template>
    <article class="cards cards--event" :class="`event-${event.id}`">
        <a class="cards__container" :href="`${event.link}`">
            <div class="cards__image">
                <figure>
                    <picture>
                        <img class="ofi-image" :src="event.thumbnail" :alt="event.title" />
                    </picture>
                </figure>
            </div>
            <div class="cards__content">
                <h3>
                    {{ event.title }}
                </h3>
                <small class="date">
                    {{ moment(event.next_events[0].date).format("DD MMMM YYYY") }}
                </small>
                <p class="excerpt">
                    {{ event.excerpt }}
                </p>
            </div>
        </a>
    </article>
</template>
<script>
import EventTypeLink from '@/components/events/EventTypeLink.vue';
import moment from "moment";

export default {
    props: {
        event: {
            type: Object,
            default: () => { },
        },
    },
    components: {
        EventTypeLink,
    },
    methods: {
        formatTime(time) {
            const hour = moment(time).format("H"); // Use 'H' for hours without leading zero
            const minute = moment(time).format("mm");

            if (minute === "00") {
                return `${hour}H`;
            } else {
                return `${hour}H${minute}`;
            }
        },
        moment(date) {
            return moment(date);
        },
    },
    setup(props) {
        return {
            event: props.event,
        };
    },
};

</script>

<style lang="scss" scoped>
.cards {
    border-radius: var(--iktickets-border-radius);

   &__container {
        display: grid;
   }

    &__image {
        position: relative;
        height: 200px;
        
        img {
            border-top-right-radius: var(--iktickets-border-radius);
            border-top-left-radius: var(--iktickets-border-radius);
        }

        .ofi-image {
            object-fit: cover;
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;

        }
    }

    &__content {
        padding: var(--iktickets-sp-md);
    }

    &--event {
        background-color: var(--iktickets-color-white);
        border-radius: var(--iktickets-border-radius);
        box-shadow: var(--iktickets-box-shadow);
        transition: box-shadow 0.3s ease-in-out;

        &:hover {
            box-shadow: var(--iktickets-box-shadow-hover);
        }

        a {
            text-decoration: none;
            color: var(--iktickets-color-black);

            h3 {
                margin-bottom: var(--iktickets-sp-sm);
                font-size: var(--iktickets-font-size-lg);
                font-family: var(--iktickets-font-iktickets-medium);
                font-weight: var(--iktickets-font-weight-iktickets-medium);
            }

            small {
                display: block;
                margin-bottom: var(--iktickets-sp-sm);
                font-size: var(--iktickets-font-size-sm);
                font-family: var(--iktickets-font-iktickets-medium);
                font-weight: var(--iktickets-font-weight-iktickets-medium);
            }

            p {
                margin-bottom: var(--iktickets-sp-sm);
                font-size: var(--iktickets-font-size-sm);
                font-family: var(--iktickets-font-iktickets-regular);
                font-weight: var(--iktickets-font-weight-iktickets-regular);
            }
        }

        .link {
            color: var(--iktickets-color-main);
        }
    }
}
</style>