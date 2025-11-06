<script setup>
import {useRoute, useRouter} from 'vue-router';
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {reactive, ref} from "vue";

const props = defineProps({
  withMainPage: Boolean
})

const route = useRoute();
const router = useRouter();

const coursesStore = useCoursesStore();

const courses = ref();
const userCourses = ref();
const modules = ref();

const segmentNames = {
  main: "Главная",
  about: "О курсе",
  course: "Курс",
  myCourses: "Мое обучение",
  profile: "Профиль",
  module: "Модуль",
  articles: "Материалы",
  homework: "Дом. задание",
  test: "Тестирование",
  progress: "Успеваемость"
}


const breadcrumbs = reactive([{
  title: segmentNames["main"],
  disabled: false,
  href: `/`
}]);

function isNumberString(str) {
  return (!isNaN(Number(str)));
}

Promise.all([
  coursesStore.availableCourses.then(data => {
    courses.value = data;
  }),

  coursesStore.userCourses?.then(data => {
    userCourses.value = data;
  }) ?? Promise.resolve(),

  coursesStore.userCourses?.[route.params.id ?? 0]?.modules?.then(data => {
    modules.value = data;
  }) ?? Promise.resolve()]
).then(_ => {

  function buildCustomName(label, id) {
    switch (label) {
      case "about": {
        return `${segmentNames[label]} "${courses.value[id].title}"`;
      }
      case "course": {
        return `${courses.value[id].title}`;
      }
      case "module": {
        return `${modules.value[id].name}`; //todo везде name позаменять на title
      }
      default: {
        return segmentNames[label];
      }
    }
  }


  const path = route.path; ///course/0/module/0/test
  const segmentedPath = path.split("/").filter((item) => {
    return item !== "";
  });  // [course, 0, module, 0, test]

  for (let i = 0; i < segmentedPath.length; i++) {
    if (!isNumberString(segmentedPath[i])) { //если на входе не числовая строка
      breadcrumbs[breadcrumbs.length] = {
        title: segmentNames[segmentedPath[i]],
        disabled: false,
        href: breadcrumbs.at(-1).href + segmentedPath[i] + "/" //такая конкатенация читается лучше, чем `${}` синтаксис в данном случае
      }; // заносим ее литерал в breadcrumbs
    }

    if ((i < segmentedPath.length) && (isNumberString(segmentedPath[i + 1]))) { // если следующий сегмент числовой
      breadcrumbs[breadcrumbs.length - 1].title = buildCustomName(segmentedPath[i], segmentedPath[i + 1]); //меняем имя на кастомизированное под сущность с id
      breadcrumbs[breadcrumbs.length - 1].href += `${segmentedPath[i + 1]}/` // пишем id литерала пути в breadcrumbs
      i++; // пропускаем один сегмент как обработанный
    }
  }

  //деактивируем последний фрагмент списка
  breadcrumbs[breadcrumbs.length - 1].disabled = true;
  breadcrumbs[breadcrumbs.length - 1].href = null;

  if (!props.withMainPage) { //если есть указание не отображать ссылку на главную страницу, удаляем ее
    breadcrumbs.shift();
  }
});
</script>

<template>
  <div class="breadcrumbs">
    <v-breadcrumbs :items="breadcrumbs">
      <template v-slot:item="{ item }">
        <v-breadcrumbs-item :href="item.href">
          {{ item.title }}
        </v-breadcrumbs-item>
      </template>
      <template v-slot:divider>
        <v-icon class="arrow-forward"></v-icon>
      </template>
    </v-breadcrumbs>
  </div>
</template>

<style scoped>

.arrow-forward {
  width: 34px;
  height: 20px;
  background-image: url("@/assets/images/arrow-forward.png");
  background-repeat: no-repeat;
  background-size: 100%;
  display: inline-block;
}

.v-breadcrumbs {
  font-size: 24px;
  font-weight: 600;
  line-height: 29px;
  color: #828282;
  padding-left: 0;
  display: flex;
  flex-wrap: wrap;
}

.v-breadcrumbs-item {
  padding: 0;
  white-space: nowrap;
}
</style>