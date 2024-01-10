import { defineStore } from "pinia";
import axios from "axios";
import { useUserStore } from "./user";
import router from "@/router/routes";

const LOCAL_STORAGE_KEY = "ikticketsCartStore";

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
    // Check if 15 minutes have passed (15 * 60 * 1000 milliseconds = 15 minutes)
    if (Date.now() - timestamp > 60 * 60 * 1000) {
        // delete pre-booked tickets
        if (data.order_id && data.cartSize > 0) {
            axios
                .delete(`/order/${data.order_id}`, {})
                .then((response) => {
                    if (response.data.status === "success") {
                        console.log(response.data.message);
                    } else {
                        console.log(response.data.message);
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        }
        localStorage.removeItem(LOCAL_STORAGE_KEY);
        this.clearCart();
        return null;
    }
    return data;
}

export const useCartStore = defineStore("cart", {
    state: () => {
        const persistedData = getFromLocalStorage();
        if (persistedData) return persistedData;

        return {
            cart: [],
            cartSize: 0,
            amount: 0,
            total: 0,
            order_id: 0,
            payment_id: 0,
            customer_id: 0,
            order: {},
            urls: {},
        };
    },
    getters: {
        getCart() {
            return this.cart || [];
        },
        getTickets() {
            return this.tickets || [];
        },
        getCartSize() {
            return this.cartSize;
        },
        getTotal() {
            return this.total;
        },
        getAmount() {
            return this.amount;
        },
        getOrder() {
            return this.order;
        },
        getOrderId() {
            return this.order_id;
        },
        getPaymentId() {
            return this.payment_id;
        },
        getPersistedData() {
            return getFromLocalStorage();
        },
        getUrls() {
            return this.urls;
        },
    },
    actions: {
        addToCart(event, category, number = 1) {
            const existingTicketIndex = this.cart.findIndex(
                (ticket) =>
                    ticket.event.event_id === event.event_id &&
                    ticket.category.tariff_id === category.tariff_id
            );

            if (existingTicketIndex !== -1) {
                // If the event-category combination already exists in the cart, just incriktickets-ease the quantity.
                this.cart[existingTicketIndex].quantity += number;
            } else {
                // Else, create a new entry with the provided event and category.
                this.cart.push({
                    event,
                    category,
                    quantity: number,
                });
            }
            this.cartSize += number;
            if (this.order_id === 0) {
                this.createOrderId();
            }
        },
        removeFromCart(event, category, number = 1) {
            const existingTicketIndex = this.cart.findIndex(
                (ticket) =>
                    ticket.event.event_id === event.event_id &&
                    ticket.category.tariff_id === category.tariff_id
            );

            if (existingTicketIndex !== -1) {
                const existingTicket = this.cart[existingTicketIndex];

                if (existingTicket.quantity <= number) {
                    // If we're removing all (or more than available) tickets of this type, remove the entry from the cart.
                    this.cart.splice(existingTicketIndex, 1);
                    this.cartSize -= existingTicket.quantity;
                } else {
                    // Otherwise, just decriktickets-ease the quantity.
                    existingTicket.quantity -= number;
                    this.cartSize -= number;
                }
            }
        },
        createOrderId() {
            axios
                .post(`/order/create`, {})
                .then((response) => {
                    if (response.data.status === "success") {
                        this.setOrderId(response.data.order_id);
                    } else {
                        console.log(response.data.message);
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        addTicketsToOrder() {
            const tickets = [];

            this.cart.forEach((ticket) => {
                const category_id = ticket.category.category_id;
                const count = ticket.quantity;

                tickets.push({ category_id, count });
            });

            axios
                .post(`/order/${this.order_id}/tickets`, {
                    tickets,
                })
                .then((response) => {
                    if (response.data.status === "success") {
                        this.setOrder(response.data.order);
                    } else {
                        console.log(response.data.message);
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        async addCustomerToOrder() {
            const userStore = useUserStore();
        
            if (userStore.getUserId) {
                this.customer_id = userStore.getUserId;
                try {
                    const response = await axios.post(`/order/${this.order_id}/customer`, {
                        customer_id: this.customer_id,
                    });
        
                    if (response.data.status === "success") {
                        console.log(response.data.message);
                    } else {
                        console.log(response.data.message);
                    }
                } catch (error) {
                    console.log(error);
                }
            } else {
                router.push({ name: "login" });
            }
        },        
        addPaymentOptionToOrder(payment_id) {
            axios
                .post(`/order/${this.order_id}/operations`, {
                    operations: [
                        {
                            payment_id,
                            amount: this.total,
                        },
                    ],
                })
                .then((response) => {
                    if (response.data.status === "success") {
                        console.log(response);
                    } else {
                        console.log(response.data.message);
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        getOrdersPayments() {
            axios
                .get(`/order/${this.order_id}/payments`)
                .then((response) => {
                    if (response.data.status === "success") {
                        this.setPaimentId(response.data.payments);
                    } else {
                        console.log(response.data.message);
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        validFreeOrder() {
            axios.put(`/order/${this.order_id}/valid`, {}).then((response) => {
                if (response.data.status === "success") {
                    router.push({ name: "checkout.success" });
                } else {
                    console.log(response.data.message);
                    router.push({ name: "checkout.error" });
                }
            });
        },
        setPaimentsUrls() {
            axios
                .get(`/order/${this.order_id}/payment`)
                .then((response) => {
                    if (response.data.status === "success") {
                        this.urls = response.data.urls;
                        saveToLocalStorage({
                            cart: this.cart,
                            total: this.total,
                            cartSize: this.cartSize,
                            amount: this.amount,
                            order_id: this.order_id,
                            payment_id: this.payment_id,
                            order: this.order,
                            urls: this.urls,
                        });
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        setPaimentId(payment_id) {
            this.payment_id = payment_id;
        },
        setTotal(total) {
            this.total = total;
        },
        setAmount(amount) {
            this.amount = amount;
        },
        setOrder(order) {
            this.order = order;
        },
        setOrderId(order_id) {
            this.order_id = order_id;
        },
        saveCart() {
            this.total += this.amount;
            this.amount = 0;
            saveToLocalStorage({
                cart: this.cart,
                total: this.total,
                cartSize: this.cartSize,
                amount: this.amount,
                order_id: this.order_id,
                payment_id: this.payment_id,
                customer_id: this.customer_id,
                order: this.order,
                urls: this.urls,
            });
            this.toggleCart();
        },
        async buyCart() {
            this.total += this.amount;
            this.amount = 0;

            // Wait for customer to be added to order
            await this.addCustomerToOrder();
            
            this.addTicketsToOrder();
            this.getOrdersPayments();
            saveToLocalStorage({
                cart: this.cart,
                total: this.total,
                cartSize: this.cartSize,
                amount: this.amount,
                order_id: this.order_id,
                payment_id: this.payment_id,
                customer_id: this.customer_id,
                order: this.order,
                urls: this.urls,
            });
            this.toggleCart();
        },
        clearCart() {
            this.cart = [];
            this.amount = 0;
            this.total = 0;
            this.cartSize = 0;
            this.order_id = 0;
            this.payment_id = 0;
            this.customer_id = 0;
            this.order = {};
            this.urls = {};
            localStorage.removeItem(LOCAL_STORAGE_KEY);
            this.toggleCart();
            // clear user
            const userStore = useUserStore();
            userStore.clear();
        },
        cancelOrder() {
            let order = {};
            // Get current order
            if (this.order_id) {
                axios.get(`/order/${this.order_id}`).then((response) => {
                    if (response.data.status === "success") {
                        console.log(response.data.order);
                        order = response.data.order;
                    } else {
                        console.log(response.data.message);
                    }
                });
            }
            // Cancel current order
            if (order && order.operations) {
                axios
                    .put(
                        `/order/${this.order_id}/cancel-operation/${order.operations.reference}`,
                        {}
                    )
                    .then((response) => {
                        if (response.data.status === "success") {
                            console.log(response.data.message);
                        } else {
                            console.log(response.data.message);
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            } else {
                if (this.order_id && this.cartSize > 0) {
                    // delete pre-booked tickets
                    axios
                        .delete(`/order/${this.order_id}`, {})
                        .then((response) => {
                            if (response.data.status === "success") {
                                console.log(response.data.message);
                            } else {
                                console.log(response.data.message);
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                }
            }
            // clear guest user
            const userStore = useUserStore();
            if (userStore.isGuest()) {
                userStore.logout();
            }
        },
        toggleCart() {
            if (this.getCartSize > 0) {
                const cartBtn = document.querySelector(".btn-cart");

                if (cartBtn) cartBtn.classList.add("has-items");
            } else {
                const cartBtn = document.querySelector(".btn-cart");

                if (cartBtn) cartBtn.classList.remove("has-items");
            }
        },
        eventCount(eventID) {
            let count = 0;
            this.cart.forEach((ticket) => {
                if (ticket.event.event_id === parseInt(eventID)) {
                    count += ticket.quantity;
                }
            });
            return count;
        },
        isEventCartFull(eventID, maxTicketsPerEvent) {
            let count = 0;
            this.cart.forEach((ticket) => {
                if (ticket.event.event_id === parseInt(eventID)) {
                    count += ticket.quantity;
                }
            });
            return count >= maxTicketsPerEvent;
        },
    },
});
