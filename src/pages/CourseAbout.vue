<script setup>
import {useRoute} from "vue-router";
import {useCoursesStore} from "@/stores/CoursesStore.js";
import Feedback from "@/components/Feedback.vue";

const route = useRoute();
const coursesStore = useCoursesStore();

const courseInfo = coursesStore?.availableCourses?.find(c => c.id === route.params.id);
</script>

<template>
  <div>
    <div class="mt-5">
      <router-link to="/" class="link-none">
        <div class="link-none backward">
          <img src="@/assets/arrow-backward.png" alt="arrow backward" class="arrow-backward"> На главную
        </div>
      </router-link>
    </div>

    <div class="title">
      Курс {{ courseInfo?.title }}
    </div>

    <div class="description">
      {{ courseInfo?.details?.longDescription }}
    </div>

    <div class="modules-title">
      В курсе {{ courseInfo?.details?.modules?.length }} модулей:
    </div>

    <div class="modules">
      <ol>
        <li v-for="module in courseInfo?.details?.modules">
          <span>{{ module }}</span>
        </li>
      </ol>
    </div>

    <div>
      <v-card elevation="0" color="#F6F8F9">
        <v-card-text>
          <div class="card-title">{{ courseInfo?.title }}</div>
          <div class="card-schedule">
            {{
              new Date(courseInfo?.details?.dateStart).toLocaleString("ru-RU", {
                year: "numeric",
                month: "long",
                day: "numeric"
              }).replace(" г.", "")
            }}
            —
            {{
              new Date(courseInfo?.details?.dateEnd).toLocaleString("ru-RU", {
                year: "numeric",
                month: "long",
                day: "numeric"
              }).replace(" г.", "")
            }}
          </div>
          <div class="card-time">
            <div>Приверное время прохождения:</div>
            <div>{{ Math.floor((courseInfo?.details?.timeEstimation / (1000 * 60 * 60)) % 24) }}</div>
          </div>
          <div class="start-button">Начать обучение</div>
        </v-card-text>
      </v-card>
    </div>

    <Feedback/>
  </div>
</template>

<style scoped>
.backward {
  font-size: 24px;
  font-weight: 600;
  line-height: 29px;
  color: #828282;
}

.arrow-backward {
  width: 34px;
}

.title {
  font-size: 40px;
  font-weight: 600;
  line-height: 49px;
}

.description {
  font-size: 20px;
  font-weight: 500;
  line-height: 27px;
}

.modules-title {
  font-size: 24px;
  font-weight: 600;
  line-height: 30px;
}

.modules {
  font-size: 20px;
  font-weight: 500;
  line-height: 20px;
}

.modules ol {
  list-style-position: inside;
}

.modules ol li span {
  padding-left: 12px;
}

.card-title {
  font-size: 26px;
  font-weight: 700;
  line-height: 33px;
}

.card-schedule {
  font-size: 16px;
  font-weight: 500;
  line-height: 20px;
}

.card-time {
  font-size: 16px;
  font-weight: 500;
  line-height: 20px;
}
</style>