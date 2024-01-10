import { defineStore } from "pinia";

export const useWpdataStore = defineStore("wpdata", {
    state: () => ({
        wpdata: {},
    }),
    getters: {
        getWpdata() {
            return this.wpdata;
        },
    },
    actions: {
        setWpdata(wpdata) {
            this.wpdata = wpdata;
        },
    },
});