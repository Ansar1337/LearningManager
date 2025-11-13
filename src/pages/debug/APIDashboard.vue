<script setup>
import {doRequest} from "@/helpers/NetworkManager.js";
import TestForm from "@/components/debug/TestForm.vue";
import {ref} from "vue";
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {useUserStore} from "@/stores/UserStore.js";
import {useRoute} from "vue-router";

const serverURL = ref(import.meta.env.VITE_API_SERVER_URL || "https://rtlm.tableer.com");

const tests = [
  {
    actor: "userManager",
    action: "getSession",
    payloadTemplate: [],
    description: "Загружает данные сессии пользователя",
    run() {
      return doRequest(this.actor, this.action);
    }
  },

  {
    actor: "userManager",
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
    actor: "userManager",
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
    actor: "userManager",
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
    actor: "userManager",
    action: "tryToLogOut",
    payloadTemplate: [],
    description: "Запрос на выход",
    run() {
      return doRequest(this.actor, this.action);
    }
  },

  {
    actor: "userManager",
    action: "loadProfileData",
    payloadTemplate: [],
    description: "Загрузка данных профиля пользователя",
    run(courseId, moduleId, fileHash) {
      return doRequest(this.actor, this.action, {
        courseId, moduleId, fileHash
      });
    }
  },

  {
    actor: "userManager",
    action: "saveProfileData",
    payloadTemplate: [
      {
        name: "Патч для профиля",
        type: "text",
        value: "{\"firstName\":\"John\",\"lastName\":\"Doe\",\"mailingSettings\":{\"digest\":false}}"
      },
    ],
    description: "Обновляет переданные поля профиля",
    run(newProfile) {
      return doRequest(this.actor, this.action, {
        newProfile
      });
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
    description: "Добавление комментария к домашнему заданию",
    run(courseId, moduleId, message) {
      return doRequest(this.actor, this.action, {
        courseId, moduleId, message
      });
    }
  },

  {
    actor: "coursesManager",
    action: "markCommentAsRead",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
      {name: "ID комментария", type: "number", value: ""},
    ],
    description: "Отметка, что комментарий прочитан",
    run(courseId, moduleId, commentId) {
      return doRequest.call(this, this.actor, this.action, {courseId, moduleId, commentId});
    }
  },

  {
    actor: "coursesManager",
    action: "markMessageAsRead",
    payloadTemplate: [
      {name: "Хэш сообщения", type: "text", value: ""},
    ],
    description: "Отметка, что сообщение прочитано",
    run(messageHash) {
      return doRequest.call(this, this.actor, this.action, {messageHash});
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
    description: "Загрузка файла ДЗ на сервер",
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
    description: "Скачивание файла ДЗ с сервера",
    run(courseId, moduleId, fileHash) {
      return doRequest(this.actor, this.action, {
        courseId, moduleId, fileHash
      });
    }
  },

  {
    actor: "coursesManager",
    action: "getUserCourseModuleTest",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
    ],
    description: "Загрузка метаданных теста",
    run(courseId, moduleId) {
      return doRequest(this.actor, this.action, {
        courseId, moduleId
      });
    }
  },

  {
    actor: "coursesManager",
    action: "launchUserCourseModuleTest",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
    ],
    description: "Старт теста",
    run(courseId, moduleId) {
      return doRequest(this.actor, this.action, {
        courseId, moduleId
      });
    }
  },

  {
    actor: "coursesManager",
    action: "getUserCourseModuleTestQuestions",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
    ],
    description: "Старт теста",
    run(courseId, moduleId) {
      return doRequest(this.actor, this.action, {
        courseId, moduleId
      });
    }
  },

  {
    actor: "coursesManager",
    action: "updateUserCourseModuleTest",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
      {name: "ID вопроса", type: "number", value: "0"},
      {name: "Ответы:", type: "text", value: "{\"Один\":false,\"Двое\":false,\"null pointer exception\":true}"},
    ],
    description: "Обновление ответов",
    run(courseId, moduleId, questionId, answers) {
      return doRequest.call(this, this.actor, this.action, {
        courseId, moduleId, questionId, answers
      });
    }
  },

  {
    actor: "coursesManager",
    action: "finishUserCourseModuleTest",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
    ],
    description: "Завершение теста",
    run(courseId, moduleId) {
      return doRequest.call(this, this.actor, this.action, {
        courseId, moduleId
      });
    }
  },

  {
    actor: "coursesManager",
    action: "reviewUserCourseModuleTest",
    payloadTemplate: [
      {name: "ID курса (0-3)", type: "number", value: "0"},
      {name: "ID модуля (0-3)", type: "number", value: "0"},
    ],
    description: "Оценка теста",
    run(courseId, moduleId) {
      return doRequest.call(this, this.actor, this.action, {
        courseId, moduleId
      });
    }
  },

  {
    actor: "coursesManager",
    action: "getUnreadMessages",
    payloadTemplate: [],
    description: "Выгрузка непрочитанных уведомлений",
    run() {
      return doRequest.call(this, this.actor, this.action);
    }
  },
];

window.TTT = true;
const user = useUserStore();
const courses = useCoursesStore();
const ready = ref(false);
const route = useRoute();

function updateQuestion() {
  courses.userCourses[0].modules[0].resources.test.questions[1].options["Травку жевал"] = !courses.userCourses[0].modules[0].resources.test.questions[1].options["Травку жевал"];
  console.log(courses.userCourses[0].modules[0].resources.test.questions[1].options["Травку жевал"]);
  console.log(courses.userCourses[0].modules[0].resources.test.questions[1].options);
}

//
// setTimeout(() => {
//   console.log(courses.availableCourses[0].details);
// }, 3000)
//
// setTimeout(() => {
//   window.DDD = true;
//   console.log(courses.availableCourses[0].details);
// }, 2000)
</script>

<template>
  <main>
    <button @click="console.log(courses.availableCourses[0].details)">ТЫЦ</button>
    <pre>
             <button
                 @click="courses.userCourses[0].modules[0].resources.articles[0].content[0].completed = false">СМЕНА ГОТОВНОСТИ
                      </button>
          {{ courses.userCourses[0].modules[0].resources.articles[0].content[0].completed }}
          <hr>
                  {{ user.session }}
                      <button
                          @click="courses?.userCourses?.[0]?.modules?.[0]?.resources?.test?.tools?.launch()">ЗАПУСК
                      </button>
                      <button @click="updateQuestion">ОТВЕТ</button>
                      <button
                          @click="courses?.userCourses?.[0]?.modules?.[0]?.resources?.test?.tools?.finish()">ФИНИШ</button>
                  {{ courses?.userCourses?.[0]?.modules?.[0]?.resources?.test?.questions?.[1]?.options }}
                        <hr>
                         <button @click="courses.unreadMessages[0].watched = true">ПРОЧИТАТЬ СООБЩЕНИЕ</button>
                      {{ courses.unreadMessages }}
                              <hr>
                         <button
                             @click="courses.userCourses[0].modules[0].resources.homework.comments[3].unread = false">ПРОЧИТАТЬ КОММЕНТАРИЙ</button>
                      {{ courses?.userCourses?.[0]?.modules?.[0]?.resources?.homework }}
                    </pre>
    <h1>Дэшборд для теста API-хэндлов</h1>
    <h2>Используемый сервер: {{ serverURL }}</h2>


    <TestForm v-for="(test, index) in tests" :key="index"
              :index="index"
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