<script setup>
import {onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {formatDate, formatLongEstimate} from "@/helpers/Formatters.js";
import Breadcrumbs from "@/components/Breadcrumbs.vue";

const coursesStore = useCoursesStore();
const route = useRoute();
const course = ref();
const details = ref();
const modules = ref();
const firstUnfinishedModuleId = ref(-1);


coursesStore.availableCourses[route.params.id].then(data => {
  course.value = data;
});

coursesStore.userCourses[route.params.id].then(data => {
  details.value = data;
});

coursesStore.userCourses[route.params.id].modules.then(data => {
  modules.value = data;

  for (let i = 0; i < data.length; i++) {
    const module = data[i];

    Promise.all([
      module.resources.test.value.review.then(review => {
        module.performance += review.score ?? 0;
        if ((!review.passed) && (firstUnfinishedModuleId.value < 0)) {
          firstUnfinishedModuleId.value = module.id;
        }
      }),
      module.resources.homework.value.then(homework => {
        module.performance += homework.score ?? 0;
      })
    ]).then(_ => {
      module.performance /= 2;
    });
  }
});

onMounted(() => {
  window.scrollTo({
    top: 0,
    left: 0,
    behavior: 'smooth'
  });
});

</script>

<template>
  <div>
    <div class="mt-5">
      <Breadcrumbs></Breadcrumbs>
    </div>
    <div>
      <v-card elevation="0" color="#F6F8F9" class="pt-2 pb-2">
        <v-card-text>
          <div class="card-content-info">
            <div class="card-content-info-title">
              <div class="course-title">Курс "{{ course?.title ? course?.title : "" }}"</div>
              <div class="time-schedule">
                <div>{{ formatDate(course?.details?.dateStart) }}</div>
                <div class="separator"></div>
                <div>{{ formatDate(course?.details?.dateEnd) }}</div>
              </div>
            </div>

            <div class="card-content-completeness">
              <div class="card-content-completeness-progress">
                <v-progress-linear color="#6FCF97" :model-value="Number(details?.completeness)" :height="7"
                                   rounded="2"></v-progress-linear>
              </div>
              <div class="percent">{{ details?.completeness }}%</div>
            </div>

            <div class="card-content-continue">
              <div class="last-lesson">
                Вы остановились на модуле <span
                  class="text-summer-sky">{{ modules?.[firstUnfinishedModuleId]?.name }}</span>
              </div>
              <div>
                <v-btn
                    :to="{name: 'module', params: { id: route.params.id, mid: firstUnfinishedModuleId }}"
                    color="#2D9CDB" class="start-btn bg-summer-sky text-white mt-2 text-none">
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
      <v-card v-for="(module) in modules"
              elevation="0"
              color="#F6F8F9"
              class="module-card"
              :class="{ done: (module?.resources?.test?.review?.passed)}">
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
                <span>{{ formatLongEstimate(module?.estimatedTime?.total) }}</span>
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
</style>