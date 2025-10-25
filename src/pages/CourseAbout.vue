<script setup>
import {useRoute} from "vue-router";
import {useCoursesStore} from "@/stores/CoursesStore.js";
import Feedback from "@/components/Feedback.vue";
import {ref} from "vue";

const route = useRoute();
const coursesStore = useCoursesStore();

let course = ref([]);
let details = ref(null);

coursesStore.availableCourses.then(r => {
  course.value = r[route.params.id];
  return r[route.params.id].details.value;
}).then(r => {
  details.value = r;
});

function formatDate(date) {
  return date
      ? new Date(date)
          .toLocaleString("ru-RU", {year: "numeric", month: "long", day: "numeric"})
          .replace(" г.", "")
      : "";
}

function formatEstimate(estimate) {
  if (estimate) {
    let hours = Math.floor((estimate / (1000 * 60 * 60)));
    if (hours >= 10 && hours <= 20 || [5, 6, 7, 8, 9, 0].includes(hours % 10))
      hours += " часов";
    else if ([2, 3, 4].includes(hours % 10))
      hours += " часа";
    else
      hours += " час";
    return hours;
  }
}

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
      Курс {{ course?.title }}
    </div>

    <div class="course-about">
      <div class="course-content">
        <div class="description" v-html="details?.longDescription"/>

        <div class="modules-title">
          В курсе {{ details?.modules?.length }} модулей:
        </div>

        <div class="modules">
          <ol>
            <li v-for="module in details?.modules || []">
              <span>{{ module }}</span>
            </li>
          </ol>
        </div>
      </div>

      <div class="course-card">
        <div>
          <v-card elevation="0" color="#F6F8F9" class="course-card-content">
            <v-card-text class="card-content">
              <div class="card-title">{{ course?.title }}</div>
              <div class="card-schedule">
                {{ formatDate(details?.dateStart) }}
                —
                {{ formatDate(details?.dateEnd) }}
              </div>
              <div class="card-time">
                <div>Примерное время прохождения:</div>
                <div class="time-hours-content mt-1">
                  <div class="time-icon"></div>
                  <div>{{ formatEstimate(details?.timeEstimation) }}</div>
                </div>
              </div>
              <button class="start-btn bg-summer-sky text-white mt-2">Начать обучение</button>
            </v-card-text>
          </v-card>
        </div>
      </div>
    </div>

    <Feedback/>
  </div>
</template>

<style scoped>
.course-about {
  display: flex;
  flex-wrap: wrap-reverse;
  gap: 20px;
}

.course-content {
  display: flex;
  flex-direction: column;
  flex-basis: max(420px, calc(100% - 420px));
  flex-grow: 1;
  gap: 20px;
}

.course-card {
  flex-basis: 400px;
  flex-grow: 1;
}

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

.course-card-content {
  min-height: 180px;
  max-width: 400px;
  min-width: 300px;
}

.card-title {
  font-size: 26px;
  font-weight: 700;
  line-height: 33px;
}

.card-content {
  padding-left: 25px;
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

.start-btn {
  border-radius: 5px;
  padding: 2px 20px 2px 20px;
  font-size: 16px;
  line-height: 20px;
  height: 30px;
}

.time-icon {
  display: inline-block;
  width: 18px;
  height: 18px;
  background-image: url("@/assets/time.png");
  background-repeat: no-repeat;
  background-size: 100%;
}

.time-hours-content {
  display: flex;
  gap: 10px;
}

</style>