import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router/routes";
import { createI18n } from "vue-i18n";
import messages from "./i18n/messages";
import { useWpdataStore } from "./stores/wpdata";
import { useEventsStore } from "./stores/events";
import { useCartStore } from "./stores/cart";
import axios from "axios";
import "./mixins"; // Import utils functions

document.addEventListener("DOMContentLoaded", function() {
    // Attach an event listener to the form submit
    const form = document.querySelector("form.form-iktickets-sync");
    if (form === null) return;
    form.addEventListener("submit", function() {
        // Show the sync animation
        const submit = form.querySelector(".button-primary");
        submit.setAttribute("disabled", "disabled");
        submit.value = "Synchronisation en cours...";
        const list = document.querySelector(".iktickets-events-list");
        list.classList.add("is-loading");
    });
});

/**
 * Create the Vue app and mount it to the #iktickets element.
 */

if (document.getElementById("iktickets")) {
    // prefix all axios requests
    const path = window.location.pathname;
    const routes = ["/billetterie", "/ticketing", "/kasse"];
    
    let prefix = "";
    if (/^\/[^/]+\/billetterie\/$/.test(path)) {
        const pathParts = path.split("/");
        prefix = `/${pathParts[1]}`;
    }

    axios.defaults.baseURL = `${prefix}/wp-json/api/v1/iktickets`;

    const pinia = createPinia();
    // Get the lang attribute from the #iktickets element
    const appElement = document.getElementById("iktickets");
    const theme = appElement.getAttribute("data-theme");
    const lang = appElement.getAttribute("data-lang");
    const wpdata = JSON.parse(appElement.getAttribute("data-content")) || {};
    const events = appElement.getAttribute("data-events") ? JSON.parse(appElement.getAttribute("data-events")) : [];

    // update --iktickets-color-main
    document.documentElement.style.setProperty("--iktickets-color-main", theme);

    const i18n = createI18n({
        locale: lang || "fr",
        fallbackLocale: "fr",
        messages,
        legacy: false,
    });

    createApp(App)
        .use(pinia)
        .use(router) // Add router plugin
        .use(i18n) // Add i18n plugin
        .mount("#iktickets"); // Mount the app to the #iktickets element

    // Create the wpdata and events stores
    const wpdataStore = useWpdataStore();
    const eventsStore = useEventsStore();
    const cartStore = useCartStore();

    // Set the wpdata and events in the store
    wpdataStore.setWpdata(wpdata);
    eventsStore.setEvents(events);

    document.addEventListener("click", (event) => {
        if (event.target.tagName === "A") {
            const href = event.target.getAttribute("href");

            if (href.startsWith("/billetterie") || href.startsWith("/ticketing") || href.startsWith("/kasse")) {
                event.preventDefault();
                router.push(href.replace("/billetterie", ""));
            }
        }
    });

    cartStore.toggleCart();
}
