<script setup>
import {ref} from "vue";
import {useRoute} from "vue-router";
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {formatDate, formatLongEstimate} from "@/helpers/Formatters.js";

const coursesStore = useCoursesStore();
const route = useRoute();
const course = ref({});
const details = ref({});
const modules = ref([])

coursesStore.availableCourses
    .then(courses => courses[route.params.id].details.value)
    .then(_ => course.value = coursesStore.availableCourses[route.params.id]);

coursesStore.userCourses
    .then(courses => courses[route.params.id].modules.value)
    .then(result => {
      details.value = coursesStore.userCourses[route.params.id];
      modules.value = result;
    });

</script>

<template>
  <div>
    <div class="mt-5">
      <!-- TODO: breadcrumbs -->
      <router-link to="/" class="link-none">
        <div class="link-none forward">
          Моё обучение
          <div class="arrow-forward"></div>
          {{ course?.title ? `${course?.title}` : "" }}
        </div>
      </router-link>
    </div>

    <div>
      <v-card elevation="0" color="#F6F8F9" class="pt-2 pb-2">
        <v-card-text>
          <div class="card-content-info">
            <div class="card-content-info-title">
              <div class="course-title">Курс "{{ course?.title ? `${course?.title}` : "" }}"</div>
              <div class="time-schedule">
                <div>{{ formatDate(course?.details?.dateStart) }}</div>
                <div class="separator"></div>
                <div>{{ formatDate(course?.details?.dateEnd) }}</div>
              </div>
            </div>

            <div class="card-content-completeness">
              <div class="card-content-completeness-progress">
                <v-progress-linear color="#6FCF97" :model-value="details?.completeness" :height="7"
                                   rounded="2"></v-progress-linear>
              </div>
              <div class="percent">{{ details?.completeness }}%</div>
            </div>

            <div class="card-content-continue">
              <div class="last-lesson">
                <!-- TODO: ссылка на последнюю главу -->
                Вы остановились на главе <span class="text-summer-sky">Java Core</span>
              </div>
              <div>
                <!-- TODO: ссылка на последнюю главу -->
                <v-btn color="#2D9CDB" class="start-btn bg-summer-sky text-white mt-2 text-none">
                  Продолжить обучение
                </v-btn>
              </div>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </div>

    <div class="module-title">
      Модули курса ({{ modules?.length }})
    </div>

    <div class="mb-5 timeline">
      <v-card v-for="module in modules"
              elevation="0"
              color="#F6F8F9"
              class="module-card"
              :class="{ done: module?.lessonsCompleted == module?.lessonsTotal}">
        <v-card-text>
          <div class="module-card-content">
            <div class="module-card-title">{{ module?.name }}</div>
            <div class="module-card-tasks">{{ module?.lessonsCompleted }} из {{ module?.lessonsTotal }} занятия</div>
            <router-link :to="{name: 'module', params: { id: course.id, mid: module.id }}" class="link-none">
              <div class="module-card-details text-summer-sky">
                Посмотреть подробнее
              </div>
            </router-link>
            <div class="module-card-info">
              <div class="module-card-estimate text-silver">
                <span class="module-card-estimate-icon">&nbsp;</span>
                <span>до {{ formatDate(module?.deadline) }}</span>
              </div>
              <div class="module-card-time text-silver">
                <span class="module-card-time-icon">&nbsp;</span>
                <span>{{ formatLongEstimate(module?.estimatedTime) }}</span>
              </div>
              <div class="module-card-grade text-summer-sky">{{ module?.performance }} / 100 баллов</div>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </div>
  </div>
</template>

<style scoped>
.module-card {
  overflow: visible;
}

.module-card-content {
  padding-left: 80px;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.module-card-title {
  font-size: 24px;
  font-weight: 600;
  line-height: 29px;
  letter-spacing: 0;
}

.module-card-tasks {
  font-size: 20px;
  font-weight: 500;
  line-height: 24px;
  letter-spacing: 0;
}

.module-card-details {
  font-size: 20px;
  font-weight: 500;
  line-height: 24px;
  letter-spacing: 0;
  text-decoration: underline;
  text-underline-position: under;
}

.module-card-estimate {
  font-size: 22px;
  font-weight: 500;
  line-height: 27px;
  letter-spacing: 0;
}

.module-card-estimate-icon {
  display: inline-block;
  width: 35px;
  height: 27px;
  background-image: url("@/assets/images/calendar-grey.png");
  background-repeat: no-repeat;
  background-size: 28px 24px;
  background-position-y: center;
}

.module-card-time-icon {
  display: inline-block;
  width: 35px;
  height: 25px;
  background-image: url("@/assets/images/time-grey.png");
  background-repeat: no-repeat;
  background-size: 26px 22px;
  background-position-y: center;
}

.module-card-time {
  font-size: 22px;
  font-weight: 500;
  line-height: 27px;
  letter-spacing: 0;
}

.module-card-grade {
  font-size: 22px;
  font-weight: 600;
  line-height: 27px;
  letter-spacing: 0;
}

.module-card-info {
  display: flex;
  gap: 5px 25px;
  flex-wrap: wrap;
  justify-content: space-between;
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

.course-title {
  font-size: 32px;
  font-weight: 600;
  line-height: 39px;
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


.module-title {
  font-size: 36px;
  font-weight: 600;
  line-height: 44px;
  letter-spacing: 0;
}

.card-content-info {
  display: flex;
  gap: 12px;
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

.percent {
  font-size: 24px;
  font-weight: 700;
  line-height: 29px;
  letter-spacing: 0;
  color: #6FCF97;
}

.last-lesson {
  font-size: 22px;
  font-weight: 600;
  line-height: 27px;
  letter-spacing: 0;
}

.card-content-info-title {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  justify-content: space-between;
  flex-grow: 1;
}

.card-content-completeness {
  display: flex;
  gap: 20px;
  align-items: center;
}

.card-content-completeness-progress {
  flex-grow: 1;
}

.card-content-continue {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  justify-content: space-between;
  flex-grow: 1;
  align-items: center;
}


.time-schedule {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
  font-size: min(24px, 3vw);
  font-weight: 500;
  line-height: 29px;
  letter-spacing: 0;
}

.separator::before {
  content: "—";
}


/* timeline */
.timeline {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.timeline > * {
  position: relative;
  z-index: auto;
}

.timeline > *::before {
  display: block;
  content: "";
  width: 25px;
  height: 25px;
  background-color: #BDBDBD;
  border-radius: 100%;
  position: absolute;
  top: 30px;
  left: 30px;
  z-index: 2;
}

.timeline > *::after {
  display: block;
  content: "";
  width: 5px;
  height: calc(100% + 35px);
  background-color: #BDBDBD;
  position: absolute;
  top: 40px;
  left: 40px;
  z-index: 1;
  overflow: visible;
}

.timeline > *:last-child::after {
  display: none;
}

.timeline > *.done::before,
.timeline > *.done + :not(.done)::before {
  background-color: #6FCF97;
  box-shadow: 0 0 16px 0 #6FCF97;

}

.timeline > *.done::after {
  background-color: #6FCF97;
}
</style>