<script setup>
import {useUserStore} from "@/stores/UserStore.js";
import {doRequest} from "@/helpers/NetworkManager.js";
import TestForm from "@/components/debug/TestForm.vue";
import {ref} from "vue";

const serverURL = ref(import.meta.env.VITE_API_SERVER_URL || "https://rtlm.tableer.com");
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
  },

  getCourseInfo: {
    result: ref(null),
    perform: function (courseId) {
      doRequest("coursesManager", "getCourseInfo", {
        courseId
      }).then((res) => {
        actions.getCourseInfo.result.value = res;
      });
    }
  },

  getUserCourses: {
    result: ref(null),
    perform: function () {
      doRequest("coursesManager", "getUserCourses").then((res) => {
        actions.getUserCourses.result.value = res;
      });
    }
  },

  getUserCourseModules: {
    result: ref(null),
    perform: function (courseId) {
      doRequest("coursesManager", "getUserCourseModules", {courseId}).then((res) => {
        actions.getUserCourseModules.result.value = res;
      });
    }
  },

  getUserCourseModuleArticleTree: {
    result: ref(null),
    perform: function (courseId, moduleId) {
      doRequest("coursesManager", "getUserCourseModuleArticleTree", {
        courseId, moduleId
      }).then((res) => {
        actions.getUserCourseModuleArticleTree.result.value = res;
      });
    }
  },

  getUserCourseModuleArticle: {
    result: ref(null),
    perform: function (courseId, moduleId, articlePath) {
      doRequest("coursesManager", "getUserCourseModuleArticle", {
        courseId, moduleId, articlePath
      }).then((res) => {
        actions.getUserCourseModuleArticle.result.value = res;
      });
    }
  },

  markMaterialAsCompleted: {
    result: ref(null),
    perform: function (courseId, moduleId, articlePath, status) {
      doRequest("coursesManager", "markMaterialAsCompleted", {
        courseId, moduleId, articlePath, status
      }).then((res) => {
        actions.markMaterialAsCompleted.result.value = res;
      });
    }
  },
}

const stores = {
  session: useUserStore()
};

</script>

<template>
  <main>
    <h1>Дэшборд для теста API-хэндлов</h1>
    <h2>Используемый сервер: {{ serverURL }}</h2>

    <TestForm
        :description="'Загружает данные сессии пользователя'"
        :actor="'sessionManager'"
        :action="'getSession'"
        :passed-data="[]"
        :run-with="actions.getSession.perform"
        :result="actions.getSession.result.value"
    />

    <TestForm
        :description="'Проверка занятости/резервирование логина'"
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

    <TestForm
        :description="'Получение детальной информации о курсе'"
        :actor="'coursesManager'"
        :action="'getCourseInfo'"
        :passed-data="[
            {name:'ID курса (0-3)', type:'number', value:'1'},
        ]"
        :run-with="actions.getCourseInfo.perform"
        :result="actions.getCourseInfo.result.value"
    />

    <TestForm
        :description="'Получение списка курсов пользователя'"
        :actor="'coursesManager'"
        :action="'getUserCourses'"
        :passed-data="[]"
        :run-with="actions.getUserCourses.perform"
        :result="actions.getUserCourses.result.value"
    />

    <TestForm
        :description="'Получение информации о модулях по конкретному/студенту'"
        :actor="'coursesManager'"
        :action="'getUserCourseModules'"
        :passed-data="[
            {name:'ID курса (0-3)', type:'number', value:'1'},
        ]"
        :run-with="actions.getUserCourseModules.perform"
        :result="actions.getUserCourseModules.result.value"
    />

    <TestForm
        :description="'Получение дерева материалов по конкретному модулю студента'"
        :actor="'coursesManager'"
        :action="'getUserCourseModuleArticleTree'"
        :passed-data="[
            {name:'ID курса (0-3)', type:'number', value:'0'},
            {name:'ID модуля (0-3)', type:'number', value:'0'},
        ]"
        :run-with="actions.getUserCourseModuleArticleTree.perform"
        :result="actions.getUserCourseModuleArticleTree.result.value"
    />

    <TestForm
        :description="'Получение материала из дерева материалов'"
        :actor="'coursesManager'"
        :action="'getUserCourseModuleArticle'"
        :passed-data="[
            {name:'ID курса (0-3)', type:'number', value:'0'},
            {name:'ID модуля (0-3)', type:'number', value:'0'},
            {name:'Адрес узла дерева материала', type:'text', value:'0,1'},
        ]"
        :run-with="actions.getUserCourseModuleArticle.perform"
        :result="actions.getUserCourseModuleArticle.result.value"
    />

    <TestForm
        :description="'Обновление статуса материала из дерева материалов'"
        :actor="'coursesManager'"
        :action="'markMaterialAsCompleted'"
        :passed-data="[
            {name:'ID курса (0-3)', type:'number', value:'0'},
            {name:'ID модуля (0-3)', type:'number', value:'0'},
            {name:'Адрес узла дерева материала', type:'text', value:'0,1'},
            {name:'Статус (true/false)', type:'checkbox', checked: false},
        ]"
        :run-with="actions.markMaterialAsCompleted.perform"
        :result="actions.markMaterialAsCompleted.result.value"
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