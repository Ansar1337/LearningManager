<script setup>
import {useSessionStore} from "@/stores/SessionStore.js";
import {doRequest} from "@/helpers/NetworkManager.js";
import TestForm from "@/components/debug/TestForm.vue";
import {ref} from "vue";

const actions = {
  getSession: {
    result: ref(null),
    perform: function () {
      doRequest("sessionManager", "getSession").then((res) => {
        actions.getSession.result.value = res;
      });
    }
  },

  reserveLogin: {
    result: ref(null),
    perform: function (login) {
      doRequest("sessionManager", "reserveLogin", {
        login
      }).then((res) => {
        actions.reserveLogin.result.value = res;
      });
    }
  },

  registerLogin: {
    result: ref(null),
    perform: function (login, password) {
      doRequest("sessionManager", "registerLogin", {
        login, password
      }).then((res) => {
        actions.registerLogin.result.value = res;
      });
    }
  },

  tryToLogIn: {
    result: ref(null),
    perform: function (login, password) {
      doRequest("sessionManager", "tryToLogIn", {
        login, password
      }).then((res) => {
        actions.tryToLogIn.result.value = res;
      });
    }
  },

  tryToLogOut: {
    result: ref(null),
    perform: function () {
      doRequest("sessionManager", "tryToLogOut").then((res) => {
        actions.tryToLogOut.result.value = res;
      });
    }
  },

  getAvailableCourses: {
    result: ref(null),
    perform: function () {
      doRequest("coursesManager", "getAvailableCourses").then((res) => {
        actions.getAvailableCourses.result.value = res;
      });
    }
  }
}

const stores = {
  session: useSessionStore()
};

</script>

<template>
  <main>
    <h1>Дэшборд для теста API-хэндлов</h1>
    <TestForm
        :description="'Загружает данные сессии пользователя'"
        :actor="'sessionManager'"
        :action="'getSession'"
        :passed-data="[]"
        :run-with="actions.getSession.perform"
        :result="actions.getSession.result.value"
    />

    <TestForm
        :description="'Проверка занятости логина'"
        :actor="'sessionManager'"
        :action="'reserveLogin'"
        :passed-data="[
            {name:'Логин', type:'text', value:'John Doe'},
        ]"
        :run-with="actions.reserveLogin.perform"
        :result="actions.reserveLogin.result.value"
    />

    <TestForm
        :description="'Регистрация логина'"
        :actor="'sessionManager'"
        :action="'registerLogin'"
        :passed-data="[
            {name:'Логин', type:'text', value:'John Doe'},
            {name:'Пароль', type:'password', value: 'Test'},
        ]"
        :run-with="actions.registerLogin.perform"
        :result="actions.registerLogin.result.value"
    />

    <TestForm
        :description="'Запрос на вход'"
        :actor="'sessionManager'"
        :action="'tryToLogIn'"
        :passed-data="[
            {name:'Логин', type:'text', value:'Ansar'},
            {name:'Пароль', type:'password', value: 'Test'},
        ]"
        :run-with="actions.tryToLogIn.perform"
        :result="actions.tryToLogIn.result.value"
    />

    <TestForm
        :description="'Запрос на выход'"
        :actor="'sessionManager'"
        :action="'tryToLogOut'"
        :passed-data="[]"
        :run-with="actions.tryToLogOut.perform"
        :result="actions.tryToLogOut.result.value"
    />


    <TestForm
        :description="'Получение списка курсов'"
        :actor="'coursesManager'"
        :action="'getAvailableCourses'"
        :passed-data="[]"
        :run-with="actions.getAvailableCourses.perform"
        :result="actions.getAvailableCourses.result.value"
    />
  </main>
</template>

<style scoped>

main {
  background: #F5F5F5;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 10px 40px;
  align-content: center;
  align-items: center;
  gap: 20px;
}
</style>