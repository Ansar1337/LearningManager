<script setup>
import {useUserStore} from "@/stores/UserStore.js";

const userStore = useUserStore();
const isOpen = defineModel("isOpen", {default: false});
const step = defineModel("step", {default: 1});
</script>

<template>
  <v-dialog v-model="isOpen" width="auto">
    <v-card>
      <v-window v-model="step">
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
              isOpen = false;
            }">Войти
            </v-btn>
            <br/>
            <v-btn color="primary" block @click="() => {
              userStore.sessionTools.tryToLogOut();
              isOpen = false;
            }">Выйти
            </v-btn>
          </v-card-actions>
        </v-window-item>

        <v-window-item :value="2">
          <v-card-text>
            Lorem ipsum dolor sit amet 2.
          </v-card-text>
          <v-card-actions>
            <v-btn color="primary" block @click="isOpen = false">Close Dialog</v-btn>
          </v-card-actions>
        </v-window-item>
      </v-window>
    </v-card>
  </v-dialog>
</template>

<style scoped>

</style>