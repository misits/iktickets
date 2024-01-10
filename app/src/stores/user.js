import { defineStore } from "pinia";
import axios from "axios";
import router from "@/router/routes";

const LOCAL_STORAGE_KEY = "ikticketsUserStore";

function saveToLocalStorage(data) {
    localStorage.setItem(
        LOCAL_STORAGE_KEY,
        JSON.stringify({
            timestamp: Date.now(),
            data,
        })
    );
}

function getFromLocalStorage() {
    const storedData = localStorage.getItem(LOCAL_STORAGE_KEY);
    if (!storedData) return null;

    const { timestamp, data } = JSON.parse(storedData);
    // Check if 1 day have passed (24 * 60 * 60 * 1000 milliseconds = 1 day)
    if (Date.now() - timestamp > 24 * 60 * 60 * 1000) {
        localStorage.removeItem(LOCAL_STORAGE_KEY);
        return null;
    }
    delete data.user.password;
    delete data.user.forgot_password;
    delete data.user.expires_at;
    delete data.user.custom_fields;
    return data;
}

export const useUserStore = defineStore("user", {
    state: () => {
        const persistedData = getFromLocalStorage();
        if (persistedData) return persistedData;

        return {
            user: {},
        };
    },
    getters: {
        getUser() {
            return this.user || {};
        },
        getUserId() {
            return this.user.id || null;
        },
        getUserName() {
            return this.user.name || null;
        }
    },
    actions: {
        login(email, password) {
            axios
                .post(`/customer/connexion`, {
                    email: email,
                    password: password,
                })
                .then((response) => {
                    if (response.data.status === "success") {
                        // check if response.data.customer is object or int
                        if (typeof response.data.customer === "object") {
                            this.user = response.data.customer;
                        } else {
                            this.user = JSON.parse({id: response.data.customer});
                        }
                        saveToLocalStorage({user: this.user});
                        router.push({ name: 'cart' });
                    } else {
                        console.log(response.data.message);
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        logout() {
            this.user = {};
            localStorage.removeItem(LOCAL_STORAGE_KEY);
            window.location = "/";
        },
        clear() {
            this.user = {};
            localStorage.removeItem(LOCAL_STORAGE_KEY);
        },
        register(form) {
            axios
                .post(`/customer/create`, form)
                .then((response) => {
                    if (response.data.status === "success") {
                        this.user = response.data.customer;
                        saveToLocalStorage({user: this.user});
                        router.push({ name: 'cart' });
                    } else {
                        if (response.data.message === "Le compte client existe déjà") {
                            console.log(response.data.message);
                            this.login(form.email, '');
                        }
                    }
                })
                .catch((error) => {
                    console.log(error);
                });

            // add custom fields
            if (!this.isGuest()) {
                axios
                .put(`/customer`, {
                    custom: form.custom_fields || {}
                }).then((response) => {
                    if (response.data.status === "success") {
                        this.user = response.data.customer;
                        saveToLocalStorage({user: this.user});
                        router.push({ name: 'cart' });
                    } else {
                        if (response.data.message === "Le compte client existe déjà") {
                            console.log(response.data.message);
                            this.login(form.email, '');
                        }
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        },
        isLogged() {
            return this.user && this.user.id;
        },
        isGuest() {
            // if user only contains id, it's a guest
            return this.user && this.user.id && Object.keys(this.user).length === 1;
        }
    },
});
