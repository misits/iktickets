import { defineStore } from "pinia";

export const useEventsStore = defineStore("events", {
    state: () => ({
        events: {},
    }),
    getters: {
        getEvents() {
            if (!this.events) {
                return null;
            }
            return this.events;
        },
    },
    actions: {
        setEvents(events) {
            if (!events) {
                return;
            }
            this.events = events;
        },
        getEventBySlug(slug) {
            if (!this.events) {
                return null;
            }
            return this.events[slug] || null;
        },
        getEventById(slug, id) {
            if (!this.events) {
                return null;
            }
            return this.events[slug][id] || null;
        }
    },
});