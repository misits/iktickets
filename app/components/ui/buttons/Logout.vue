<template>
    <button class="iktickets-btn" @click="logout">{{ $t("auth.logout") }}</button>
</template>
<script setup>
import { useUserStore } from '../../../stores/user';

const userStore = useUserStore();

const logout = () => {
    userStore.logout();
};
</script>

<style lang="scss" scoped>

</style>