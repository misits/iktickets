<template>
    <div class="iktickets-myaccount-page">
      <h2 class="the-subtitle--iktickets-sp-sm">{{ $t("ticketing.my_account") }}</h2>
      <div class="user-details" v-if="user">
        <table class="user-table">
          <tbody>
            <tr v-for="(value, key, index) in user" :key="index">
              <template v-if="!exclude.includes(key) && value">
                <td>{{ $t(`auth.form.${key}`) }}</td>
                <td>{{ value }}</td>
              </template>
            </tr>
          </tbody>
        </table>
      </div>
      <Logout />
    </div>
  </template>

<script setup>
import Logout from '../components/ui/buttons/Logout.vue';
import { useUserStore } from '../stores/user';
import router from '../router/routes';
import { ref } from 'vue';

const userStore = useUserStore();
const user = ref(userStore.getUser);

const exclude = ['id', 'password', 'forgot_password', 'key', 'expires_at', 'created_at', 'updated_at'];

if (!userStore.isLogged())
    router.push({ name: 'login' });
</script>

<style lang="scss" scoped>
.iktickets-myaccount-page {
  .user-details {
  .user-table {
    border-collapse: collapse;
    margin-bottom: var(--iktickets-sp-xl);
    width: 50%;
    @media (max-width: 768px) {
        width: 100%;
    }

    th, td {
      border: 1px solid var(--iktickets-color-grey-light);
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: var(--iktickets-color-grey-light);
    }
    tr:nth-child(even) {
      background-color: var(--iktickets-color-grey-lighter);
    }
  }
}
}
</style>