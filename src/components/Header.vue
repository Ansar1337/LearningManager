<script setup>
import {ref} from "vue";
import {useUserStore} from "@/stores/UserStore.js";
import AuthDialog from "@/components/dialogs/AuthDialog.vue";

const userStore = useUserStore();
const showLoginDialog = ref(false);
</script>

<template>
  <header class="bg-white">
    <div class="header-container">
      <router-link to="/" class="link-none">
        <h1 class="header-title text-summer-sky">
          AnsarCodes
        </h1>
      </router-link>

      <div v-if="!userStore.session.loggedIn">
        <v-btn color="#2D9CDB" class="header-btn" text="Войти" @click="showLoginDialog = true" elevation="0"/>
      </div>
      <div v-else>
        <v-btn color="#F6F8F9" elevation="0" class="bell-btn">
          <img src="@/assets/btn-bell.png" width="46" alt="bell icon">
        </v-btn>

        <v-btn color="#F6F8F9" class="header-btn text-none" text="Моё обучение" elevation="0"/>

        <v-btn color="#F6F8F9" class="header-btn text-none" elevation="0">
          Профиль
          <v-menu activator="parent">
            <v-list>
              <v-list-item title="Учебная программа" :to="'/'" :active="false" class="list-item-space"/>
              <v-list-item title="Успеваемость" :to="'/'" :active="false" class="list-item-space"/>
              <v-divider/>
              <v-list-item title="Настройки профиля" :to="'/'" :active="false" class="list-item-space"/>
              <v-divider/>
              <v-list-item title="Выйти" :active="false" class="list-item-space"
                           @click="userStore.sessionTools.tryToLogOut"/>
            </v-list>
          </v-menu>
        </v-btn>
      </div>
    </div>

    <div></div>
    <AuthDialog v-model:is-open="showLoginDialog"/>
  </header>
</template>

<style scoped>
/* header */
.header-title {
  font-size: 24px;
  font-weight: 800;
}

.header-container {
  margin: 0 auto;
  padding: 10px 40px;
  display: flex;
  flex: 1 1 auto;
  max-width: min(1920px, 85%);
  align-content: center;
  align-items: center;
  justify-content: space-between;
}

.header-btn {
  font-size: 16px;
  font-weight: 600;
  letter-spacing: 0;
  margin-left: 20px;
}

.bell-btn {
  padding: 0;
  min-width: 0;
}

.list-item-space {
  min-height: 30px;
}
</style>