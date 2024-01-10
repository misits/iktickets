import { 
    createRouter, 
    createWebHashHistory, 
    createWebHistory 
} from 'vue-router';
import Billetterie from '@/views/Billetterie.vue';
import Events from '@/views/Events.vue';
import Cart from '@/views/Cart.vue';
import Checkout from '@/views/Checkout.vue';
import CheckoutSuccess from '@/views/CheckoutSuccess.vue';
import CheckoutError from '@/views/CheckoutError.vue';
import MyAccount from '@/views/MyAccount.vue';
import Order from '@/views/Order.vue';
import Login from '@/views/Login.vue';
import Register from '@/views/Register.vue';

/**
 * Routes for the Vue Router.
 */
const routes = [
    { 
        path: '/', 
        component: Billetterie,
        name: 'home',
        props: true
    },
    { 
        path: '/en/ikevents', 
        component: Billetterie,
        name: 'home_en',
        props: true
    },
    { 
        path: '/de/ikevents', 
        component: Billetterie,
        name: 'home_de',
        props: true
    },
    { 
        path: '/ikevents', 
        component: Billetterie, 
        name: 'billetterie', 
        props: true 
    },
    { 
        path: '/events/:slug',
        component: Events, 
        name: 'events', 
        props: true
    },
    { 
        path: '/events/:slug/order/:event_id',
        component: Order, 
        name: 'event-order', 
        props: true
    },
    { 
        path: '/cart', 
        component: Cart, 
        name: 'cart', 
        props: true 
    },
    {
        path: '/einkaufswagen', 
        component: Cart, 
        name: 'cart_de', 
        props: true 
    },
    { 
        path: '/checkout', 
        component: Checkout, 
        name: 'checkout', 
        props: true 
    },
    { 
        path: '/checkout/success', 
        component: CheckoutSuccess, 
        name: 'checkout.success', 
        props: true
    },
    { 
        path: '/checkout/error', 
        component: CheckoutError, 
        name: 'checkout.error', 
        props: true
    },
    { 
        path: '/my-account', 
        component: MyAccount, 
        name: 'my-account', 
        props: true 
    },
    { 
        path: '/login', 
        component: Login, 
        name: 'login', 
        props: true 
    },
    { 
        path: '/forgotpassword', 
        component: Login, 
        name: 'auth.forgot_password', 
        props: true 
    },
    { 
        path: '/register', 
        component: Register, 
        name: 'auth.register', 
        props: true
    },
    { 
        path: '/register/guest', 
        component: Register, 
        name: 'auth.register.guest', 
        props: true
    },
];


/**
 * We need to use different history modes depending on the page we are on.
 * If we are on /billetterie/ page, we need to use hash mode, otherwise we need to use history mode.
 */
let historyMode;

const path = window.location.pathname;
const paths = ["/ikevents/", "/en/ikevents/", "/de/ikevents/"];

if (paths.some(p => path.includes(p))) {
    historyMode = createWebHashHistory();
} else {
    historyMode = createWebHistory();
}

const router = createRouter({
    history: historyMode,
    routes,
});

export default router;
