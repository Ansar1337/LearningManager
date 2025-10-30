<script setup>
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {useRoute} from "vue-router";
import {ref} from "vue";
import FeedbackDialog from "@/components/dialogs/FeedbackDialog.vue";

const route = useRoute();
const coursesStore = useCoursesStore();

const showFeedbackDialog = ref(false);
</script>

<template>
  <div class="mb-5">
    <div class="mt-5">
      <!-- TODO: breadcrumbs -->
      <router-link to="/" class="link-none">
        <div class="link-none forward">
          Моё обучение
          <div class="arrow-forward"></div>
          Java
          <div class="arrow-forward"></div>
          Основы Java
        </div>
      </router-link>
    </div>

    <!-- TODO: адаптировать под экраны иконки законченности задания -->
    <!-- TODO: данные не совпадают с макетом, не времени выполнения каждой секции -->
    <div class="task-row">
      <div class="task-column">
        <div class="title task-title">Основы Java</div>
        <v-progress-circular color="#6FCF97" :model-value="20" :size="30" :width="7"/>
      </div>
      <div class="time-text">Время прохождения ~ 4 часа 30 минут</div>
    </div>

    <router-link :to="''" class="link-none">
      <div class="task-row">
        <div class="task-column">
          <div class="article-text task-title">
            <div class="materials"></div>
            Материалы
          </div>
          <div class="complete"></div>
        </div>
        <div class="time-text">4 часа 30 минут</div>
      </div>
    </router-link>

    <router-link :to="''" class="link-none">
      <div class="task-row">
        <div class="task-column">
          <div class="article-text task-title">
            <div class="homeworks"></div>
            Домашнее задание
          </div>
          <div class="uncompleted"></div>
        </div>
        <div class="time-text">4 часа 30 минут</div>
      </div>
    </router-link>

    <div class="task-row clickable" @click="showFeedbackDialog = true">
      <div class="task-column">
        <div class="article-text task-title">
          <div class="feedback"></div>
          Обратная связь по уроку
        </div>
      </div>
      <div class="time-text">4 часа 30 минут</div>
    </div>


    <router-link :to="''" class="link-none">
      <div class="task-row">
        <div class="task-column">
          <div class="article-text task-title">
            <div class="tests"></div>
            Тест по теме
          </div>
          <div class="uncompleted"></div>
        </div>
        <div class="time-text">4 часа 30 минут</div>
      </div>
    </router-link>

    <feedback-dialog v-model:is-open="showFeedbackDialog"></feedback-dialog>
  </div>
</template>

<style scoped>
.clickable {
  cursor: pointer;
}

.task-row {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.task-column {
  display: flex;
  align-items: center;
  gap: 20px;
}

.task-title {
  min-width: max(50%, 300px);
}

.arrow-forward {
  width: 34px;
  height: 15px;
  background-image: url("@/assets/images/arrow-forward.png");
  background-repeat: no-repeat;
  background-size: 100%;
  display: inline-block;
}

.forward {
  font-size: 24px;
  font-weight: 600;
  line-height: 29px;
  color: #828282;
}

.title {
  font-size: 40px;
  font-weight: 600;
  line-height: 49px;
  letter-spacing: 0;
}

.time-text {
  font-size: 22px;
  line-height: 27px;
  letter-spacing: 0;
  color: #828282;
}

.article-text {
  display: flex;
  gap: 10px;
  align-items: center;
  font-size: 24px;
  font-weight: 600;
  line-height: 29px;
  letter-spacing: 0;
  color: black;
}

.complete {
  width: 28px;
  height: 28px;
  background-image: url("../assets/images/task-complete.png");
  background-repeat: no-repeat;
  background-size: 100%;
}

.uncompleted {
  width: 28px;
  height: 28px;
  background-image: url("../assets/images/task-uncompleted.png");
  background-repeat: no-repeat;
  background-size: 100%;
}

.materials {
  width: 28px;
  height: 28px;
  background-image: url("../assets/images/materials.png");
  background-repeat: no-repeat;
  background-size: 100%;
}

.homeworks {
  width: 28px;
  height: 28px;
  background-image: url("../assets/images/homeworks.png");
  background-repeat: no-repeat;
  background-size: 100%;
}

.feedback {
  width: 28px;
  height: 28px;
  background-image: url("../assets/images/feedbacks.png");
  background-repeat: no-repeat;
  background-size: 100%;
}

.tests {
  width: 28px;
  height: 28px;
  background-image: url("../assets/images/tests.png");
  background-repeat: no-repeat;
  background-size: 100%;
}
</style>