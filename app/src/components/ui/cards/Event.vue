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
                <div class="categories">
                    <small class="category" v-for="(category, index) in event.categories" :key="index">
                        {{ category }}
                    </small>
                </div>
                <hr />
                <div class="details">
                    <div class="date">
                        <p class="day">{{ moment(event.next_events[0].date).format("DD") }}</p>
                        <p class="month">{{ moment(event.next_events[0].date).format("MMM") }}</p>
                    </div>
                    <div class="info">

                        <div class="address">
                            <p>
                                <LocationIcon :size="16" />
                                {{ event.next_events[0].address.title }}
                            </p>
                        </div>
                        <div class="time">
                            <p>
                                <TimeIcon :size="16" />
                                <span>{{ event.next_events[0].start_hour }}</span>
                                <span v-if="event.next_events[0].end_hour">-{{ event.next_events[0].end_hour }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                <hr />
                <EventTypeLink :slug="event.slug" class="btn">
                    <TicketIcon :size="25" />
                </EventTypeLink>
            </div>
        </a>
    </article>
</template>
<script>
import EventTypeLink from '@/components/events/EventTypeLink.vue';
import TicketIcon from "@/assets/images/svg/ticket.vue";
import TimeIcon from "@/assets/images/svg/time.vue";
import LocationIcon from "@/assets/images/svg/location.vue";
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
        TicketIcon,
        TimeIcon,
        LocationIcon
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

        h3 {
            margin-top: 0;
            display: inline-block;
            font-size: var(--iktickets-font-size-lg);
            font-family: var(--iktickets-font-bold);
        }
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
                margin: 0;
                font-size: var(--iktickets-font-size-lg);
                font-weight: var(--iktickets-font-weight-medium);
            }

            small {
                display: block;
                margin-bottom: var(--iktickets-sp-sm);
                font-size: var(--iktickets-font-size-sm);
                font-weight: var(--iktickets-font-weight-medium);
            }

            p {
                margin-bottom: var(--iktickets-sp-sm);
                font-size: var(--iktickets-font-size-sm);
                font-weight: var(--iktickets-font-weight-regular);
            }
        }

        hr {
            margin: var(--iktickets-sp-sm) 0;
            border: 0;
            border-top: 1px solid var(--iktickets-color-grey-lighter);
        }

        .details {
            display: flex;
            align-items: center;
            gap: var(--iktickets-sp-sm);
        }

        .category {
            color: var(--iktickets-color-main);
            font-weight: var(--iktickets-font-weight-regular);
        }


        .address,
        .time {

            p {
                display: flex;
                align-items: center;
                margin: 0;
                font-size: var(--iktickets-font-size-sm);
                font-weight: var(--iktickets-font-weight-regular);

                svg {
                    margin-right: var(--iktickets-sp-xs);
                }
            }
        }

        .date {
            display: flex;
            flex-direction: column;
            width: 50px;
            background-color: var(--iktickets-color-white);
            padding: var(--iktickets-sp-xs);
            border-radius: var(--iktickets-border-radius);

            color: var(--iktickets-color-main);

            p {
                text-align: center;
                line-height: 1;
                margin: 0;
                font-size: var(--iktickets-font-size-lg);
            }

            .day {
                font-weight: var(--iktickets-font-weight-bold);
            }

            .month {
                font-weight: var(--iktickets-font-weight-regular);
            }
        }

        .btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0 0 0 auto;
            width: fit-content;
            width: --webkit-fit-content;
            width: --moz-fit-content;
            font-size: var(--iktickets-font-size-sm);
            color: var(--iktickets-color-main);
            border: 1px solid var(--iktickets-color-main);
            border-radius: var(--iktickets-border-radius);
            background-color: var(--iktickets-color-white);
            padding: 0.3rem 0.5rem;
            transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;

            &:hover {
                background-color: var(--iktickets-color-main);
                color: var(--iktickets-color-white);
            }
        }
    }
}</style>