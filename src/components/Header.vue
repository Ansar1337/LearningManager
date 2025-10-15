<script setup>
import {onMounted, ref} from 'vue';
import {useUserStore} from "@/stores/UserStore.js";

const userStore = useUserStore();
const isShowLogin = ref(false);
const dialogStep = ref(1);

</script>

<template>
  <header class="bg-white">
    <div class="header-container">
      <router-link to="/" class="link-none">
        <h1 class="header-title text-summer-sky">
          AnsarCodes
        </h1>
      </router-link>

      <v-btn color="#2D9CDB" class="header-btn" text="Войти" @click="isShowLogin = true"/>
    </div>

    <div></div>
  </header>

  <!-- block in development -->
  <v-dialog v-model="isShowLogin" width="auto">
    <v-card>
      <v-window v-model="dialogStep">
        <v-window-item :value="1">
          <v-card-text>
            <div>Войдите в аккаунт, чтобы начать учиться</div>
            <div>Почта</div>
            <input type="text" style="border: 1px solid gray">
            <div>Пароль</div>
            <input type="text" style="border: 1px solid gray">
            <div>Не помню пароль</div>
          </v-card-text>
          <v-card-actions style="flex-wrap: wrap">
            <v-btn color="primary" block @click="() => {
              userStore.sessionTools.tryToLogIn('ansar', '11111').then(result => console.log(result));
            }">Войти
            </v-btn>
            <br/>
            <v-btn color="primary" block @click="() => {
              userStore.sessionTools.tryToLogOut();
            }">Выйти
            </v-btn>
          </v-card-actions>
        </v-window-item>

        <v-window-item :value="2">
          <v-card-text>
            Lorem ipsum dolor sit amet 2.
          </v-card-text>
          <v-card-actions>
            <v-btn color="primary" block @click="isShowLogin = false">Close Dialog</v-btn>
          </v-card-actions>
        </v-window-item>
      </v-window>
    </v-card>
  </v-dialog>
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
}

/* login dialog */

</style>