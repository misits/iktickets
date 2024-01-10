<template>
    <section class="login-container">
        <h2 class="the-subtitle--iktickets-sp-sm">
            <span v-if="!isGuest">{{ $t("auth.register.title") }}</span>
            <span v-else>{{ $t("auth.register.title_guest") }}</span>
        </h2>
        <p class="description">
            <span v-if="!isGuest">{{ $t("auth.register.description") }}</span>
            <span v-else>{{ $t("auth.register.description_guest") }}</span>
        </p>
        <form class="login-form" @submit.prevent="register">
            <div class="form-field" v-if="!isGuest">
                <label for="civility"
                    >{{ $t("auth.form.civility")
                    }}<span class="required">*</span></label
                >
                <select id="civility" name="civility" v-model="form.civility">
                    <option
                        v-for="option in CIVILITIES"
                        :value="option"
                        :key="option.slugify('_')"
                    >
                        {{ $t("auth.form." + option) }}
                    </option>
                </select>
            </div>
            <div class="form-field" v-if="!isGuest">
                <label for="firstname"
                    >{{ $t("auth.form.firstname")
                    }}<span class="required">*</span></label
                >
                <input
                    type="text"
                    id="firstname"
                    name="firstname"
                    v-model="form.firstname"
                    required
                />
            </div>
            <div class="form-field" v-if="!isGuest">
                <label for="lastname"
                    >{{ $t("auth.form.lastname")
                    }}<span class="required">*</span></label
                >
                <input
                    type="text"
                    id="lastname"
                    name="lastname"
                    v-model="form.lastname"
                    required
                />
            </div>
            <div class="form-field">
                <label for="email"
                    >{{ $t("auth.form.email")
                    }}<span class="required">*</span></label
                >
                <input
                    type="email"
                    id="email"
                    name="email"
                    v-model="form.email"
                    required
                />
            </div>
            <div class="form-field" v-if="!isGuest">
                <label for="password"
                    >{{ $t("auth.form.password")
                    }}<span class="required">*</span></label
                >
                <input
                    type="password"
                    id="password"
                    name="password"
                    v-model="form.password"
                    required
                />
            </div>
            <div class="form-field" v-if="!isGuest">
                <label for="password_confirmation"
                    >{{ $t("auth.form.password_confirmation")
                    }}<span class="required">*</span></label
                >
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    v-model="form.password_confirmation"
                    required
                />
            </div>
            <div class="form-field" v-if="!isGuest">
                <label for="language">{{ $t("auth.form.language") }}</label>
                <select id="language" name="language" v-model="form.language">
                    <option
                        v-for="option in LANGUAGES"
                        :value="option"
                        :key="option.slugify('_')"
                    >
                        {{ $t("language." + option) }}
                    </option>
                </select>
            </div>
            <div class="form-field" v-if="!isGuest">
                <label for="firm">{{ $t("auth.form.firm") }}</label>
                <input type="text" id="firm" name="firm" v-model="form.firm" />
            </div>
            <div class="form-field" v-if="!isGuest">
                <label for="iktickets-phone">{{ $t("auth.form.iktickets-phone") }}</label>
                <input
                    type="text"
                    id="iktickets-phone"
                    name="iktickets-phone"
                    v-model="form.phone"
                />
            </div>
            <div class="form-field" v-if="!isGuest">
                <label for="address">{{ $t("auth.form.address") }}</label>
                <input
                    type="text"
                    id="address"
                    name="address"
                    v-model="form.address"
                />
            </div>
            <div class="form-field" v-if="!isGuest">
                <label for="city">{{ $t("auth.form.city") }}</label>
                <input type="text" id="city" name="city" v-model="form.city" />
            </div>
            <div class="form-field" v-if="!isGuest">
                <label for="zipcode">{{ $t("auth.form.zipcode") }}</label>
                <input
                    type="text"
                    id="zipcode"
                    name="zipcode"
                    v-model="form.zipcode"
                />
            </div>
            <div class="form-field" v-if="!isGuest">
                <label for="country">{{ $t("auth.form.country") }}</label>
                <select id="country" name="country" v-model="form.country">
                    <option
                        v-for="option in COUNTRIES"
                        :value="option"
                        :key="option.slugify('_')"
                    >
                        {{ $t("country." + option.slugify("_")) }}
                    </option>
                </select>
            </div>
            <div class="form-field form-field--checkbox" v-if="!isGuest">
                <label for="newsletter">{{ $t("auth.form.newsletter") }}</label>
                <input
                    type="checkbox"
                    id="newsletter"
                    name="newsletter"
                    v-model="form.newsletter"
                />
            </div>
            <!--<div class="form-field form-field--checkbox" v-if="!isGuest">
                <label for="newsletter_paper">{{ $t("auth.form.newsletter_paper") }}</label>
                <input type="checkbox" id="newsletter_paper" name="newsletter_paper" v-model="form.custom[0].newsletter_papier" />
            </div>-->
            <button class="iktickets-btn">
                <span v-if="!isGuest">{{ $t("auth.register.submit") }}</span
                ><span v-else>{{ $t("ticketing.buy_cart") }}</span>
            </button>
        </form>
        <div class="links">
            <router-link :to="{ name: 'login' }">{{
                $t("auth.already_account")
            }}</router-link>
            <router-link :to="{ name: 'checkout' }" v-if="!isGuest">{{
                $t("auth.continue_as_guest")
            }}</router-link>
        </div>
    </section>
</template>
<script setup>
import { useUserStore } from "@/stores/user";
import { COUNTRIES, CIVILITIES, LANGUAGES } from "@/constants";
import { ref } from "vue";
import { useRoute } from "vue-router";

const userStore = useUserStore();
const route = useRoute();
const isGuest = ref(false);

const form = ref({
    civility: "Mr",
    firstname: "",
    lastname: "",
    email: "",
    password: "",
    password_confirmation: "",
    language: "fr",
    firm: "",
    phone: "",
    address: "",
    city: "",
    zipcode: "",
    country: "SWITZERLAND",
    newsletter: true,
    custom: [
        {
            newsletter_papier: true,
        },
    ],
});

// get current route name
if (route.name === "auth.register.guest") {
    // hide all fields
    isGuest.value = true;
}

const register = () => {
    if (!isGuest && form.value.password !== form.value.password_confirmation) {
        alert("Les mots de passe ne correspondent pas");
        return;
    }

    // remove password confirmation
    delete form.value.password_confirmation;

    if (isGuest) delete form.value.newsletter;

    userStore.register(form.value);
};
</script>

<style lang="scss" scoped>
.login-container {
    display: flex;
    flex-direction: column;
    height: 100%;
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

    .required {
        color: var(--iktickets-color-red);
    }

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
        padding: 0.5em 0;
        border-bottom: 2px solid var(--iktickets-color-main);
        font-size: var(--iktickets-font-size-sm);
        outline: none;
    }

    select {
        border-top: none;
        border-left: none;
        border-right: none;
        border-bottom: 2px solid var(--iktickets-color-main);
        font-size: var(--iktickets-font-size-sm);
        padding: 0.5em 0;
        &:focus {
            outline: none;
        }
    }

    &--checkbox {
        display: flex;
        flex-direction: row;

        label {
            flex: 1;
            margin-bottom: 0;
            cursor: pointer;
        }

        input {
            margin-right: var(--iktickets-sp-sm);
            flex: 0;
            cursor: pointer;
        }
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
