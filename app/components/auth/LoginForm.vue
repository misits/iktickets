<template>
    <section class="login-container">
        <h2 class="the-subtitle--iktickets-sp-sm">{{ $t("auth.login.title") }}</h2>
        <p class="description">{{ $t("auth.login.description") }}</p>
        <form class="login-form" @submit.prevent="login">
            <div class="form-field">
                <label for="email">{{ $t("auth.identifier") }}</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    v-model="email"
                    required
                />
            </div>
            <div class="form-field">
                <label for="password">{{ $t("auth.password") }}</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    v-model="password"
                    required
                />
            </div>
            <button class="iktickets-btn iktickets-btn-back">
                {{ $t("auth.submit") }}
            </button>
        </form>
        <div class="links">
            <router-link :to="{ name: 'auth.forgot_password' }">{{
                $t("auth.forgot_password")
            }}</router-link>
            <router-link :to="{ name: 'auth.register' }">{{
                $t("auth.create_account")
            }}</router-link>
            <router-link :to="{ name: 'auth.register.guest' }">{{
                $t("auth.continue_as_guest")
            }}</router-link>
        </div>
    </section>
</template>
<script setup>
import { useUserStore } from "../../stores/user";
import { ref } from "vue";

const userStore = useUserStore();

const email = ref("");
const password = ref("");

const login = () => {
    userStore.login(email.value, password.value);
};
</script>

<style lang="scss" scoped>
.login-container {
    display: flex;
    flex-direction: column;
    width: 80%;

    @media (max-width: 768px) {
        width: 100%;
    }

    .title {
        font-size: var(--iktickets-font-size-xl);
        margin: var(--iktickets-sp-title) 0 var(--iktickets-sp-md);
    }

    .description {
        font-size: var(--iktickets-font-size-lg);
        margin: 0 0 var(--iktickets-sp-xl) 0;
    }
}

.login-form {
    display: flex;
    flex-direction: column;
    height: 100%;
    width: 100%;
}

.form-field {
    display: flex;
    flex-direction: column;
    margin-bottom: var(--iktickets-sp-lg);

    label {
        display: block;
        text-transform: uppercase;
        color: var(--iktickets-color-main);
        font-size: var(--iktickets-font-size-sm);
        margin-bottom: var(--iktickets-sp-sm);
    }

    input {
        display: block;
        width: 100%;
        border-top: 0;
        border-left: 0;
        border-right: 0;
        border-bottom: 2px solid var(--iktickets-color-main);
        font-size: var(--iktickets-font-size-sm);
        outline: none;
    }
}

.links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    margin: 115px 0 2rem;
    gap: 1rem 3rem;
    a {
        display: block;
        text-transform: uppercase;
        color: var(--iktickets-color-main);
        border-bottom: 1px solid var(--iktickets-color-main);
        font-size: var(--iktickets-font-size-sm);
        text-decoration: none;
        // margin-right: 45px;
    }
}
</style>
