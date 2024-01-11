<template>
    <div class="iktickets-events-page">
        <div class="event-data" v-if="event">
            <h2 class="title">{{ event.title }}</h2>

            <div class="categories">
                <small class="category" v-for="(category, index) in event.categories" :key="index">
                    {{ category }}
                </small>
            </div>
            <div class="image">
                <figure>
                    <picture>
                        <img class="ofi-image" :src="event.thumbnail" :alt="event.title" />
                    </picture>
                </figure>
            </div>
            <hr />
            <p>
                {{ $t("text.next_event") }}
            </p>
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
            
            <div v-html="event.content"></div>
        </div>
        <div class="event-calendar">
            <h2 class="the-subtitle">{{ $t("ticketing.select_date") }}</h2>
            <Calendar />
        </div>
    </div>
</template>

<script setup>
import Calendar from '@/components/events/Calendar.vue';
import TimeIcon from "@/assets/images/svg/time.vue";
import LocationIcon from "@/assets/images/svg/location.vue";
import moment from "moment";
import { useEventsStore } from '@/stores/events';
import { onMounted, ref } from 'vue';

const eventsStore = useEventsStore();
const event = ref(null);

onMounted(async () => {
    event.value = await eventsStore.getEvent;
});

</script>

<style lang="scss" scoped>
.iktickets-events-page {
    display: grid;
    grid-template-columns: 2fr 1fr;
    grid-gap: 4rem;

    hr {
        margin: var(--iktickets-sp-sm) 0;
        border: 0;
        border-top: 1px solid var(--iktickets-color-grey-lighter);
    }

    .image {
        position: relative;
        height: calc(var(--vh, 1vh) * 30);

        img {
            border-radius: var(--iktickets-border-radius);
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

    .title {
        margin-bottom: 0;
    }

    .category {
        color: var(--iktickets-color-main);
    }

    .details {
        display: flex;
        align-items: center;
        gap: var(--iktickets-sp-sm);
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
        border-radius: var(--iktickets-border-radius);
        box-shadow: var(--iktickets-box-shadow);
        transition: box-shadow 0.3s ease-in-out;

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

    @media screen and (max-width: 768px) {
        grid-template-columns: 1fr;
        grid-gap: 0;
    }
}
</style>