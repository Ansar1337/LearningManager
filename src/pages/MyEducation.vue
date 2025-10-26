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
  // TODO: проблемы типов, id string и number в разных частях
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
  // TODO: проблемы типов, id string и number в разных частях
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
                <!-- TODO: нет больших картинок для языков -->
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
  font-size: 36px;
  font-weight: 600;
  line-height: 44px;
  letter-spacing: 0;
}

.time-schedule {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
  font-size: 24px;
  font-weight: 500;
  line-height: 29px;
  letter-spacing: 0;
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