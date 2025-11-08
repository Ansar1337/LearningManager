<script setup>
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {ref, watch} from "vue";
import {formatDateShort} from "@/helpers/Formatters.js";
import {useUserStore} from "@/stores/UserStore.js";
import {useRouter} from "vue-router";

const router = useRouter();
const coursesStore = useCoursesStore();

const courseInfo = ref(false);
const courseDetails = ref(false);
const showAll = ref(false);

coursesStore.availableCourses.then(courses => {
  Promise.all(courses.map(c => c.details.value)).then(_ => {
    courseInfo.value = true;
  })
});

coursesStore.userCourses.then(_ => courseDetails.value = true);

function isUserCourse(id) {
  return coursesStore.userCourses.map(c => parseInt(c.id)).includes(parseInt(id));
}

function getCompleteness(id) {
  return coursesStore.userCourses.find(uc => parseInt(uc.id) === parseInt(id)).completeness;
}

</script>

<template>
  <div class="mb-5">
    <div class="title mt-5">
      Моё обучение
    </div>
    <div class="course-card-list">
      <template v-if="courseInfo && courseDetails" v-for="(course, index) in coursesStore.availableCourses"
                :key="index">
        <template v-if="showAll || isUserCourse(course.id)">
          <v-card elevation="0" color="#F6F8F9" :key="course.id" class="pt-2 pb-2">
            <v-card-text class="card-content">

              <div class="logo-holder">
                <!-- TODO: нет больших картинок для языков -->
                <img src="../assets/images/java-big-logo.png" alt="language logo" class="card-image">
              </div>

              <div class="card-content-info">
                <div class="card-content-text">
                  <div class="card-title">Курс "{{ course.title }}"</div>
                  <div class="time-schedule">
                    <div>{{ formatDateShort(course?.details?.dateStart) }}</div>
                    <div class="separator"></div>
                    <div>{{ formatDateShort(course?.details?.dateEnd) }}</div>
                  </div>
                </div>
                <div v-if="isUserCourse(course.id)" class="card-content-progress">
                  <v-progress-circular color="#6FCF97" :model-value="getCompleteness(course.id)" :size="55"
                                       :width="10"/>
                </div>
                <div class="card-content-open-button">
                  <div class="mt-2">
                    <v-btn v-if="isUserCourse(course.id)"
                           color="#2D9CDB"
                           class="start-btn bg-summer-sky text-white mt-2 text-none"
                           :to="{name: 'coursePage', params: { id :  course.id}}">
                      Продолжить обучение
                    </v-btn>
                    <v-btn color="#2D9CDB" v-else class="start-btn bg-summer-sky text-white mt-2 text-none">
                      <!-- TODO: начать обучение, не ясно, что делать при нажатии -->
                      Начать обучение
                    </v-btn>
                  </div>
                </div>
              </div>

            </v-card-text>
          </v-card>
        </template>
      </template>
      <template v-else>Загрузка...</template>
    </div>

    <div class="text-right">
      <v-btn class="start-btn border-summer-sky mt-2 text-none" elevation="0" @click="showAll = !showAll">
        <span class="text-summer-sky">{{ showAll ? 'Показать мои курсы' : 'Посмотреть все курсы' }}</span>
      </v-btn>
    </div>
  </div>
</template>

<style scoped>

.title {
  font-size: 40px;
  font-weight: 600;
  line-height: 49px;
  letter-spacing: 0;
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
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0;
}

.border-summer-sky {
  border: 1px solid #2D9CDB;
}

.card-title {
  font-size: min(36px, 4vw);
  font-weight: 500;
  line-height: 44px;
  letter-spacing: 0;
}

.time-schedule {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
  font-size: min(24px, 3vw);
  /*font-weight: 500;*/
  line-height: 29px;
  letter-spacing: 0;
}

.card-content {
  display: flex;
  flex-wrap: nowrap;
  padding-left: 0;
}

.card-content-info {
  display: flex;
  column-gap: 15px;
  row-gap: 15px;
  flex-wrap: wrap;
  justify-content: flex-start;
  flex-grow: 1;
  align-items: center;
}

.card-image {
  width: min(100px, 7vw);
  min-width: 50px;
  height: fit-content;
}

.logo-holder {
  min-width: 85px;
  max-width: 290px;
  width: 20%;
  display: flex;
  justify-content: center;
}

.card-content-text {
  flex-basis: max(375px, 80%);
  margin-right: auto;
}

.card-content-progress > div {
  min-width: 35px !important;
  width: 7vw !important;
  aspect-ratio: 1 !important;
}

.card-content-open-button {

}

.card-content-open-button .mt-2 {
  margin: 0 !important;
}

.separator::before {
  content: "—";
}

@media only screen and (max-width: 758px) {
  .separator::before {
    content: "/";
  }

  .v-btn.start-btn {
    width: 100%;
    box-sizing: border-box;
  }

  .card-content-info,
  .card-content-text,
  .time-schedule {
    flex-basis: fit-content;
  }
}

</style>