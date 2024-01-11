import { defineStore } from "pinia";
import axios from "axios";
import router from "@/router/routes";

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
    async getEvent() {
      // Current route params
      const { slug } = router.currentRoute.value.params;

      return await axios
        .get(`/event/${slug}`)
        .then((response) => {
          return response.data;
        })
        .catch((error) => {
          console.log(error);
        });
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
    },
  },
});
