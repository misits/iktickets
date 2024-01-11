<template>
    <div class="calendar">
        <div class="calendar-header">
            <button @click="prevMonth" class="month-switcher">
                <PrevIcon />
            </button>
            <p class="calendar-month">
                {{ $t(`months.${currentMonth.format("MMMM").toLowerCase()}`) }}
                {{ currentMonth.format("YYYY") }}
            </p>
            <button @click="nextMonth" class="month-switcher">
                <NextIcon />
            </button>
        </div>

        <!-- Combined days and dates into one grid container -->
        <div class="calendar-grid">
            <!-- Days -->
            <div v-for="day in days" :key="day" class="day-name">{{ day }}</div>

            <!-- Dates -->
            <div
                v-for="dateObj in dates"
                :key="dateObj.date.format()"
                :class="{
                    'calendar-date': true,
                    today: dateObj.date.isSame(today(), 'day'),
                    'not-current-month': !dateObj.isCurrentMonth,
                }"
                @click="setSelectDate(dateObj.events)"
            >
                <p class="date">{{ dateObj.date.date() }}</p>
                <!-- Display event if it exists for the date -->
                <div class="events-items">
                    <div
                        :class="{
                            'has-event-mobile': true,
                            past: isPast(dateObj.date),
                        }"
                        v-if="dateObj.events"
                    ></div>
                    <div
                        class="events"
                        v-if="dateObj.events"
                        v-for="event in dateObj.events"
                        :key="event.id"
                    >
                        <p class="time" v-if="!isPast(event.date)">
                            <EventBuyLink :event_id="event.event_id.toString()">
                                {{ formatTime(event.date) }}
                            </EventBuyLink>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="event-mobile" v-if="selectedDate">
            <p>{{ moment(selectedDate[0].date).format("DD MMMM YYYY") }}</p>
            <div class="events-items">
                <div
                    class="events"
                    v-for="event in selectedDate"
                    :key="event.id"
                >
                    <p class="time" v-if="!isPast(event.date)">
                        <EventBuyLink :event_id="event.event_id.toString()">
                            {{ formatTime(event.date) }}
                        </EventBuyLink>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import EventBuyLink from "./EventBuyLink.vue";
import { ref, computed, watch } from "vue";
import moment from "moment";
import { useI18n } from "vue-i18n";
import PrevIcon from "@/assets/images/svg/prev.vue";
import NextIcon from "@/assets/images/svg/next.vue";

export default {
    name: "MonthlyCalendar",
    components: {
        EventBuyLink,
        PrevIcon,
        NextIcon,
    },
    props: {
        events: {
            type: Object,
            default: () => {},
        },
        daysLayout: {
            type: String,
            default: "short",
        },
    },
    setup(props) {
        const { t } = useI18n();
        const currentMonth = ref(moment());
        const today = () => moment();
        const events = props.events;
        const selectedDate = ref(null);
        const days = [
            t(`days.${props.daysLayout}.monday`),
            t(`days.${props.daysLayout}.tuesday`),
            t(`days.${props.daysLayout}.wednesday`),
            t(`days.${props.daysLayout}.thursday`),
            t(`days.${props.daysLayout}.friday`),
            t(`days.${props.daysLayout}.saturday`),
            t(`days.${props.daysLayout}.sunday`),
        ];

        const populateDates = (dateObj) => {
            const formattedDate = dateObj.format("YYYY-MM-DD");
            const matchedEvents = [];
            for (const eventId in events) {
                const event = events[eventId];
                if (event.start && event.start.startsWith(formattedDate)) {
                    matchedEvents.push(event); // If found, return the event
                }
            }

            // Sort events by time
            matchedEvents.sort((a, b) => {
                return moment(a.start).diff(moment(b.start));
            });

            return matchedEvents.length > 0 ? matchedEvents : null;
        };

        const setSelectDate = (events) => {
            if (!events || !selectedDate.value) {
                selectedDate.value = null;
            }

            if (
                selectedDate.value &&
                selectedDate.value[0].date_id === events[0].date_id
            ) {
                selectedDate.value = null;
                return;
            } else {
                selectedDate.value = events ? events : null;
            }
        };

        const dates = computed(() => {
            let startOfMonth = currentMonth.value.clone().startOf("month");
            let endOfMonth = currentMonth.value.clone().endOf("month");

            // Adjusting for Monday start
            let startDay =
                startOfMonth.day() === 0 ? 6 : startOfMonth.day() - 1; // Adjust for Monday start

            let daysInMonth = currentMonth.value.daysInMonth();

            let totalDaysNeeded;
            if (startDay + daysInMonth > 35) {
                totalDaysNeeded = 42;
            } else {
                totalDaysNeeded = 35;
            }

            let dates = [];

            // Fill dates before start of the month
            for (let i = startDay - 1; i >= 0; i--) {
                dates.push({
                    date: startOfMonth.clone().subtract(i + 1, "days"),
                    isCurrentMonth: false,
                });
            }

            // Fill the actual dates of the month
            for (let i = 1; i <= daysInMonth; i++) {
                const currentDate = moment([
                    currentMonth.value.year(),
                    currentMonth.value.month(),
                    i,
                ]);

                dates.push({
                    date: moment([
                        currentMonth.value.year(),
                        currentMonth.value.month(),
                        i,
                    ]),
                    isCurrentMonth: true,
                    events: populateDates(currentDate),
                });
            }

            // Add days from the next month until we get the required total days
            let nextMonthDay = 1;
            while (dates.length < totalDaysNeeded) {
                dates.push({
                    date: endOfMonth.clone().add(nextMonthDay, "days"),
                    isCurrentMonth: false,
                });
                nextMonthDay++;
            }

            return dates;
        });

        const prevMonth = () => {
            currentMonth.value = currentMonth.value
                .clone()
                .subtract(1, "month");
        };

        const nextMonth = () => {
            currentMonth.value = currentMonth.value.clone().add(1, "month");
        };

        const isPast = (date) => {
            // is before today and same day hour is before now
            return moment(date).isBefore(today());
        };

        return {
            currentMonth,
            days,
            dates,
            events,
            selectedDate,
            prevMonth,
            nextMonth,
            today,
            populateDates,
            isPast,
            setSelectDate,
        };
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
};
</script>

<style lang="scss" scoped>
*,
*:before,
*:after {
    box-sizing: border-box;
}

.month-switcher {
    background-color: inherit;
    border: none;
    color: var(--iktickets-color-main);
    cursor: pointer;
}

.calendar {
    width: 100%;
}

.calendar-header {
    display: flex;
    margin-bottom: var(--iktickets-sp-xxl);
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    /* 7 columns for 7 days of the week */
    grid-gap: 0;
    // all same size
    grid-template-rows: repeat(6, 1fr);
}

.day-name,
.calendar-date {
    margin: 0;
}

.day-name {
    text-align: center;
  
}

.calendar-month {
    margin: 0;
    padding: 0 var(--iktickets-sp-xl);
}

.calendar-date {
    text-align: center;
    border-top: 1px solid var(--iktickets-calendar-border-color, #ddd);
    border-left: 1px solid var(--iktickets-calendar-border-color, #ddd);
    transition: background-color 0.2s;

    .date {
        margin-top: 12px;
        line-height: 1;
    }

    .events-items {
        position: relative;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-gap: 0.5rem;
        padding: 0.5rem;
    }

    .events {
        display: flex;
        width: 100%;
        align-items: center;
        align-content: center;
        justify-content: center;
        flex-wrap: wrap;
        text-align: center;

        &:hover {
            .time {
                background-color: var(--iktickets-color-grey);
                color: var(--iktickets-color-black);
            }
        }

        .time {
            font-size: var(--iktickets-font-size-sm);
            margin: 0;
            padding: 2px 6px;
            width: 100%;
            text-align: center;
            background-color: var(--iktickets-color-main);
            color: var(--iktickets-color-white);
            transition: background-color 0.2s;
            a {
                color: var(--iktickets-color-white);
                text-decoration: none;
                display: inline-block;
                width: 100%;
            }
            @media screen and (max-width: 1400px) {
                font-size: var(--iktickets-font-size-xs);
            }

            @media screen and (max-width: 1280px) {
                display: none;
            }
        }
    }
}

.has-event-mobile {
    display: none;

    &.past {
        display: none;
    }

    // iktickets-tablet
    @media screen and (max-width: 1280px) {
        display: block;
        width: 6px;
        height: 6px;
        position: absolute;
        border-radius: 50%;
        top: -6px;
        left: 50%;
        transform: translateX(-50%);
        background-color: var(--iktickets-color-main);
    }
}

.event-mobile {
    display: none;
    &.hidden {
        display: none;
    }

    // iktickets-tablet
    @media screen and (max-width: 1280px) {
        display: block;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: var(--iktickets-color-white);
        z-index: 100;
        padding: var(--iktickets-sp-sm);
        box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);

        .events-items {
            position: relative;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-gap: 0.5rem;
            margin-top: var(--iktickets-sp-xs);
        }

        .events {
            display: flex;
            width: 100%;
            align-items: center;
            align-content: center;
            justify-content: center;
            flex-wrap: wrap;
            text-align: center;

            &:hover {
                .time {
                    background-color: var(--iktickets-color-grey);
                    color: var(--iktickets-color-black);
                }
            }

            .time {
                font-size: var(--iktickets-font-size-sm);
                margin: 0;
                display: inline-block;
                padding: 2px 6px;
                width: 100%;
                text-align: center;
                background-color: var(--iktickets-color-main);
                color: var(--iktickets-color-white);
                a {
                    text-decoration: none;
                    color: var(--iktickets-color-white);
                    display: inline-block;
                    width: 100%;
                }
            }
        }
    }
}

// add border rigth to 7nth day
.calendar-date:nth-child(7n) {
    border-right: 1px solid var(--iktickets-calendar-border-color, #ddd);
}

// add border bottom to last row
.calendar-date:nth-last-child(-n + 7) {
    border-bottom: 1px solid var(--iktickets-calendar-border-color, #ddd);
}

.calendar-date.today {
    background-color: var(--iktickets-calendar-today-color, inherit);
}

.not-current-month {
    color: var(--calendar-not-current-month-color, #aaa);
}
</style>
