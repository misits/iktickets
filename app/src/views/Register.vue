<template>
    <div class="iktickets-register-page">
        <RegisterForm />
    </div>
</template>
<script setup>
import RegisterForm from '@/components/auth/RegisterForm.vue';
import { useUserStore } from '@/stores/user';
import router from '@/router/routes';

const userStore = useUserStore();

if (userStore.isLogged()) {
    router.push({ name: 'billetterie' });
}
</script>