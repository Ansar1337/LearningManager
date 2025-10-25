<script setup>
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {ref} from "vue";

const coursesStore = useCoursesStore();

const courseInfo = ref(false);
const courseDetails = ref(false);
const showAll = ref(false);

coursesStore.availableCourses.then(courses => {
  Promise.all(courses.map(c => c.details.value)).then(_ => {
    courseInfo.value = true
  })
});
coursesStore.userCourses.then(_ => courseDetails.value = true);

function isUserCourse(id) {
  // TODO: type problem, id number and id string
  return coursesStore.userCourses.map(c => c.id + '').includes(id + '');
}

function formatDate(date) {
  return date
      ? new Date(date)
          .toLocaleString("ru-RU", {year: "numeric", month: "long", day: "numeric"})
          .replace(" г.", "")
      : "";
}

function getCompleteness(id) {
  // TODO: type problem, id number and id string
  return coursesStore.userCourses.find(uc => uc.id + '' === id).completeness
}
</script>

<template>
  <div class="mb-5">
    <div class="title mt-5">
      Моё обучение
    </div>

    <div class="course-card-list">
      <template v-if="courseInfo && courseDetails" v-for="course in coursesStore.availableCourses">
        <template v-if="showAll || isUserCourse(course.id)">
          <v-card elevation="0" color="#F6F8F9" :key="course.id" class="pt-2 pb-2">
            <v-card-text class="card-content">

              <div>
                <!-- TODO: no big image for languages in data -->
                <img src="@/assets/java-big-logo.png" alt="language logo" class="card-image">
              </div>

              <div class="card-content-info">
                <div>
                  <div class="card-title">{{ course.title }}-разработчик</div>
                  <div class="time-schedule">
                    <div>{{ formatDate(course?.details?.dateStart) }}</div>
                    <div> —</div>
                    <div>{{ formatDate(course?.details?.dateEnd) }}</div>
                  </div>
                  <div class="mt-2">
                    <button class="start-btn bg-summer-sky text-white mt-2">
                      {{ isUserCourse(course.id) ? 'Продолжить обучение' : 'Начать обучение' }}
                    </button>
                  </div>
                </div>

                <div v-if="isUserCourse(course.id)">
                  <v-progress-circular color="#6FCF97" :model-value="getCompleteness(course.id)" :size="55"
                                       :width="10"/>
                </div>
              </div>

            </v-card-text>
          </v-card>
        </template>
      </template>
    </div>

    <div class="text-right">
      <button class="start-btn border-summer-sky text-summer-sky mt-2" @click="showAll = !showAll">
        {{ showAll ? 'Показать мои курсы' : 'Посмотреть все курсы' }}
      </button>
    </div>
  </div>
</template>

<style scoped>
.title {
  font-size: 40px;
  font-weight: 600;
  line-height: 49px;
}

.course-card-list {
  display: flex;
  gap: 10px;
  flex-direction: column;
}

.start-btn {
  border-radius: 5px;
  padding: 2px 20px 2px 20px;
  font-size: 16px;
  line-height: 20px;
  height: 30px;
}

.border-summer-sky {
  border: 1px solid #2D9CDB;
}

.card-title {
  font-size: 36px;
  font-weight: 600;
  line-height: 44px;
}

.time-schedule {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
  font-size: 24px;
  font-weight: 500;
  line-height: 29px;
}

.card-content {
  display: flex;
  gap: 40px;
  flex-wrap: wrap;
}

.card-content-info {
  display: flex;
  gap: 40px;
  flex-wrap: wrap;
  justify-content: space-between;
  flex-grow: 1;
}

.card-image {
  width: 100px;
}
</style>