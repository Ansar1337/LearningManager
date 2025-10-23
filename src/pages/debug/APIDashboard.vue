<script setup>
import {doRequest} from "@/helpers/NetworkManager.js";
import TestForm from "@/components/debug/TestForm.vue";
import {ref} from "vue";
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {useUserStore} from "@/stores/UserStore.js";

const serverURL = ref(import.meta.env.VITE_API_SERVER_URL || "https://rtlm.tableer.com");

const tests = [
  {
    actor: "sessionManager",
    action: "getSession",
    payloadTemplate: [],
    description: "Загружает данные сессии пользователя",
    run() {
      return doRequest(this.actor, this.action);
    }
  },

  {
    actor: "sessionManager",
    action: "reserveLogin",
    payloadTemplate: [
      {name: "Логин", type: "text", value: "John Doe"},
    ],
    description: "Проверка занятости/резервирование логина",
    run(login) {
      return doRequest(this.actor, this.action, {login});
    }
  },

  {
    actor: "sessionManager",
    action: "registerLogin",
    payloadTemplate: [
      {name: "Логин", type: "text", value: "John Doe"},
      {name: "Пароль", type: "password", value: "Test"},
    ],
    description: "Регистрация логина",
    run(login, password) {
      return doRequest(this.actor, this.action, {login, password});
    }
  },

  {
    actor: "sessionManager",
    action: "tryToLogIn",
    payloadTemplate: [
      {name: "Логин", type: "text", value: "Ansar"},
      {name: "Пароль", type: "password", value: "Test"},
    ],
    description: "Запрос на вход",
    run(login, password) {
      return doRequest(this.actor, this.action, {login, password});
    }
  },

  {
    actor: "sessionManager",
    action: "tryToLogOut",
    payloadTemplate: [],
    description: "Запрос на выход",
    run() {
      return doRequest(this.actor, this.action);
    }
  },

  {
    actor: "coursesManager",
    action: "getAvailableCourses",
    payloadTemplate: [],
    description: "Получение списка курсов",
    run() {
      return doRequest(this.actor, this.action);
    }
  },

  {
    actor: "coursesManager",
    action: "getCourseInfo",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "1"},
    ],
    description: "Получение детальной информации о курсе",
    run(courseId) {
      return doRequest(this.actor, this.action, {courseId});
    }
  },

  {
    actor: "coursesManager",
    action: "getUserCourses",
    payloadTemplate: [],
    description: "Получение списка курсов пользователя",
    run() {
      return doRequest(this.actor, this.action);
    }
  },

  {
    actor: "coursesManager",
    action: "getUserCourseModules",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "1"},
    ],
    description: "Получение информации о модулях по конкретному курсу/студенту",
    run(courseId) {
      return doRequest(this.actor, this.action, {courseId});
    }
  },

  {
    actor: "coursesManager",
    action: "getUserCourseModuleArticlesTree",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
    ],
    description: "Получение дерева материалов по конкретному модулю студента",
    run(courseId, moduleId) {
      return doRequest(this.actor, this.action, {courseId, moduleId});
    }
  },

  {
    actor: "coursesManager",
    action: "getUserCourseModuleArticle",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
      {name: "Адрес узла дерева материала", type: "text", value: "0,1"},
    ],
    description: "Получение материала из дерева материалов",
    run(courseId, moduleId, articlePath) {
      return doRequest(this.actor, this.action, {
        courseId, moduleId, articlePath
      });
    }
  },

  {
    actor: "coursesManager",
    action: "markMaterialAsCompleted",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
      {name: "Адрес узла дерева материала", type: "text", value: "0,1"},
      {name: "Статус (true/false)", type: "checkbox", checked: false},
    ],
    description: "Обновление статуса материала из дерева материалов",
    run(courseId, moduleId, articlePath, status) {
      return doRequest(this.actor, this.action, {
        courseId, moduleId, articlePath, status
      });
    }
  },

  {
    actor: "coursesManager",
    action: "getUserCourseModuleHomework",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
    ],
    description: "Выгрузка данных о домашних заданиях в модуле",
    run(courseId, moduleId) {
      return doRequest(this.actor, this.action, {
        courseId, moduleId
      });
    }
  },

  {
    actor: "coursesManager",
    action: "addHomeworkComment",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
      {name: "Сообщение", type: "text", value: ""},
    ],
    description: "Выгрузка данных о домашних заданиях в модуле",
    run(courseId, moduleId, message) {
      return doRequest(this.actor, this.action, {
        courseId, moduleId, message
      });
    }
  },

  {
    actor: "coursesManager",
    action: "addHomeworkSubmission",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
      {name: "Сообщение", type: "file"},
    ],
    description: "Выгрузка данных о домашних заданиях в модуле",
    run(courseId, moduleId, file) {
      return doRequest(this.actor, this.action, {
        courseId, moduleId, file
      });
    }
  },

  {
    actor: "coursesManager",
    action: "downloadHomeworkFile",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
      {name: "Хэш файла", type: "text", value: "4b828daee3c2a4bf3ec375468a8d4fdf"},
    ],
    description: "Выгрузка данных о домашних заданиях в модуле",
    run(courseId, moduleId, fileHash) {
      return doRequest(this.actor, this.action, {
        courseId, moduleId, fileHash
      });
    }
  }
];

const user = useUserStore();
const courses = useCoursesStore();
let sd1 = ref(null);
let sd2 = ref(null);
let sd3 = ref(null);
let sd4 = ref(null);
// courses.availableCourses[0].details.value.longDescription.then(console.log);
// courses.availableCourses[0]?.details.value.modules[0].then(r => sd2.value = r);

// courses.availableCourses[0].details.value.then(r => sd2.value = r);
// courses.availableCourses[0].details.value.modules[0].then(console.log);

// courses.availableCourses[1].details.modules[0].then(console.log);
// courses.availableCourses[1].details.value.modules.then(console.log);


courses.availableCourses
    .then(r => r[0])
    .then(r => r.details)
    .then(r => sd1.value = r);

courses.availableCourses[0].details.value.then(r => sd2.value = r);


setTimeout(() => {
  courses.availableCourses[0].details.then(r => sd3.value = r);
}, 1000);

setTimeout(() => {
  courses.availableCourses
      .then(r => r[0])
      .then(r => r.details)
      .then(r => sd4.value = r);
}, 2000);

</script>

<template>
  <main>
        <pre>
          <button @click="user.sessionTools.tryToLogIn('ansar', '123')">ТЫЦ</button>
          <button @click="user.sessionTools.tryToLogOut()">антЫЦ</button>
          {{ user.session }}
          1
          {{ sd1 }}
          2
          {{ sd2 }}
          3
          {{ sd3 }}
          4
          {{ sd4 }}
                    auto
                    {{ courses?.availableCourses[0] }}
        </pre>

    <h1>Дэшборд для теста API-хэндлов</h1>
    <h2>Используемый сервер: {{ serverURL }}</h2>

    <TestForm v-for="(test, index) in tests" :key="index"
              :description="test.description"
              :actor="test.actor"
              :action="test.action"
              :passed-data="test.payloadTemplate"
              :run-with="test.run"
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