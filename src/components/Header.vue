<script setup>
import {ref} from "vue";
import {useUserStore} from "@/stores/UserStore.js";
import AuthDialog from "@/components/dialogs/AuthDialog.vue";
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {useRouter} from "vue-router";

const userStore = useUserStore();
const courses = useCoursesStore();
const router = useRouter();
const showLoginDialog = ref(false);
const messages = ref([]);

courses.unreadMessages.then(result => messages.value = result);

function navigateToModule(message) {
  message.watched = true;
  router.push({name: 'module', params: {id: message.course, mid: message.module}})
}
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
      <div v-else class="user-panel">
        <v-btn color="#F6F8F9" elevation="0" class="bell-btn" :class="{ active: messages.length > 0}">
          <v-menu activator="parent" max-height="300" v-if="messages.length > 0">
            <v-list>
              <v-list-item v-for="message in messages" @click="navigateToModule(message)">
                <div class="notification-menu-content">
                  {{ courses?.availableCourses[message.course]?.title }}
                  >
                  {{ courses?.userCourses[message.course]?.modules[message.module]?.name }}
                  :
                  {{ message?.type === 'deadline' ? ' срок выполнения подходит к концу' : ' новое сообщение' }}
                </div>
              </v-list-item>
            </v-list>
          </v-menu>
        </v-btn>
        <v-btn color="#F6F8F9" class="header-btn text-none" :to="{name: 'myEducation'}" text="Моё обучение"
               elevation="0"/>

        <v-btn color="#F6F8F9" class="header-btn text-none" elevation="0">
          Профиль
          <v-menu activator="parent">
            <v-list>
              <v-list-item title="Учебная программа" :to="{name: 'myEducation'}" :active="false"
                           class="list-item-space"/>
              <v-list-item title="Успеваемость" :to="{name: 'progress'}" :active="false" class="list-item-space"/>
              <v-divider/>
              <v-list-item title="Настройки профиля" :to="{name: 'profile'}" :active="false" class="list-item-space"/>
              <v-divider/>

              <v-list-item title="Выйти" :to="'/'" :active="false" class="list-item-space"
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
.notification-menu-content {
  min-width: 300px;
  min-height: 30px;
  padding: 8px;
  cursor: pointer;
}

.notification-menu-content:hover {
  background-color: #BDBDBD;
  border-radius: 6px;
}

/* header */
.header-title {
  font-size: 24px;
  font-weight: 800;
}

.header-container {
  margin: 0 auto;
  padding: 10px 0;
  display: flex;
  flex: 1 1 auto;
  max-width: min(1920px, 85%);
  align-content: center;
  align-items: center;
  justify-content: space-between;
  transition: 0.3s ease;
}

.header-btn {
  font-size: 16px;
  font-weight: 600;
  letter-spacing: 0;
}

.bell-btn {
  padding: 0;
  min-width: 0;
  width: 46px;
  height: 37px;
  background-image: url("../assets/images/btn-bell.png");
  background-repeat: no-repeat;
  background-size: 100%;
}

.bell-btn.active {
  background-image: url("../assets/images/btn-bell-active.png");
}

.list-item-space {
  min-height: 30px;
}

.user-panel {
  display: flex;
  column-gap: 20px;
}

@media only screen and (max-width: 758px) {
  .header-container {
    padding: 10px 0;
    flex-wrap: wrap;
  }

  .user-panel {
    display: flex;
    column-gap: 5px;
  }
}
</style>