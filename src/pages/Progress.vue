<script setup>
import {useUserStore} from "@/stores/UserStore.js";
import {ref} from "vue";
import {formatDate} from "@/helpers/Formatters.js";
import {useCoursesStore} from "@/stores/CoursesStore.js";

const userStore = useUserStore();
const coursesStore = useCoursesStore();

const info = ref({});
userStore.session.profile
    .then(result => result.value ? result.value : result)
    .then(result => info.value = result);

const courseInfo = ref(false);
const courseDetails = ref(false);

coursesStore.availableCourses.then(courses => {
  Promise.all(courses.map(c => c.details)).then(_ => {
    courseInfo.value = true
  })
});

coursesStore.userCourses.then(_ => courseDetails.value = true);

</script>

<template>
  <div v-if="courseInfo && courseDetails" class="mb-5">
    <div class="title mt-5">
      Успеваемость
    </div>

    <div class="card">

      <div class="card-user-info">
        <div class="user-picture"></div>
        <div class="user-info">
          {{ info?.lastName }} {{ info?.firstName }}
        </div>
      </div>

      <div class="small-title">
        Пройденные курсы
      </div>

      <template v-for="course in coursesStore.userCourses">
        <div v-if="course?.completeness == 100" class="card-text" :key="course?.id">
          <div class="card-text-inside">
            <div class="card-text-about">
              <div>
                <v-progress-circular color="#6FCF97" :model-value="Number(course?.completeness)" :size="55"
                                     :width="10"/>
              </div>
              <div>
                <div class="card-language">
                  {{ coursesStore?.availableCourses?.filter(r => r.id == course.id)?.[0]?.title }}
                </div>
                <div>Ваш ID {{ userStore?.session?.userId }}</div>
                <!-- TODO: <div>Группа </div> -->
              </div>
            </div>
            <div>
              <!-- TODO: средняя оценка? -->
              Средняя оценка <span class="module-card-grade text-summer-sky">9.8 баллов</span>
              <div class="time-schedule">
                <div>{{
                    formatDate(coursesStore?.availableCourses?.filter(r => r.id == course.id)?.[0]?.details?.dateStart)
                  }}
                </div>
                <div class="separator"></div>
                <div>{{
                    formatDate(coursesStore?.availableCourses?.filter(r => r.id == course.id)?.[0]?.details?.dateEnd)
                  }}
                </div>
              </div>
            </div>
          </div>
          <div>
            <router-link :to="{name: 'coursePage', params: { id :  course.id}}" class="link-none">
              <div class="module-card-details text-summer-sky">подробнее про курс</div>
            </router-link>
          </div>
        </div>
      </template>

      <div class="small-title">
        Курсы в процессе
      </div>

      <template v-for="course in coursesStore.userCourses">
        <div v-if="course?.completeness != 100" class="card-text" :key="course?.id">
          <div class="card-text-inside">
            <div class="card-text-about">
              <div>
                <v-progress-circular color="#6FCF97" :model-value="Number(course?.completeness)" :size="55"
                                     :width="10"/>
              </div>
              <div>
                <div class="card-language">
                  {{ coursesStore?.availableCourses?.filter(r => r.id == course.id)?.[0]?.title }}
                </div>
                <div>Ваш ID {{ userStore?.session?.userId }}</div>
                <!-- TODO: <div>Группа </div> -->
              </div>
            </div>
            <div>
              <!-- TODO: средняя оценка? -->
              Средняя оценка <span class="module-card-grade text-summer-sky">9.8 баллов</span>
              <div class="time-schedule">
                <div>{{
                    formatDate(coursesStore?.availableCourses?.filter(r => r.id == course.id)?.[0]?.details?.dateStart)
                  }}
                </div>
                <div class="separator"></div>
                <div>{{
                    formatDate(coursesStore?.availableCourses?.filter(r => r.id == course.id)?.[0]?.details?.dateEnd)
                  }}
                </div>
              </div>
            </div>
          </div>
          <div>
            <router-link :to="{name: 'coursePage', params: { id :  course.id}}" class="link-none">
              <div class="module-card-details text-summer-sky">подробнее про курс</div>
            </router-link>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<style scoped>
.card-text {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.card-text-inside {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.card-text-about {
  display: flex;
  gap: 20px;
}

.time-schedule {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
  font-size: 18px;
  letter-spacing: 0;
}

.separator::before {
  content: "—";
}

.title {
  font-size: 40px;
  font-weight: 600;
  line-height: 49px;
  letter-spacing: 0;
}

.module-card-grade {
  font-size: 20px;
  font-weight: 700;
  line-height: 24px;
  letter-spacing: 0;
}

.card {
  background-color: #F6F8F9;
  padding: 20px;
  border-radius: 6px;
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  flex-direction: column;
}

.card-user-info {
  display: flex;
  gap: 40px;
  align-items: center;
  flex-wrap: wrap;
}

.card-language {
  font-size: 24px;
  font-weight: 600;
  line-height: 29px;
  letter-spacing: 0;
}

.card-text {
  font-size: 18px;
  font-weight: 500;
  letter-spacing: 0;
}

.module-card-details {
  font-size: 18px;
  line-height: 22px;
  letter-spacing: 0;
  text-decoration: underline;
  text-underline-position: under;
}

.user-picture {
  position: relative;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  background-image: url("@/assets/images/profile-picture.png");
}

.user-info {
  font-size: 28px;
  font-weight: 600;
  line-height: 34px;
  letter-spacing: 0;
}

.small-title {
  font-size: 32px;
  font-weight: 600;
  line-height: 39px;
  letter-spacing: 0;
}

</style>