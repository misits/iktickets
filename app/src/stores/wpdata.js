import { defineStore } from "pinia";

export const useWpdataStore = defineStore("wpdata", {
    state: () => ({
        wpdata: {},
    }),
    getters: {
        getWpdata() {
            return this.wpdata;
        },
        getPosts() {
            return this.wpdata.posts;
        }
    },
    actions: {
        setWpdata(wpdata) {
            this.wpdata = wpdata;
        },
    },
});