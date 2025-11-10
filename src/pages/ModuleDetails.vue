<script setup>
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {useRoute} from "vue-router";
import {onMounted, ref} from "vue";
import {formatLongEstimate} from "@/helpers/Formatters.js";
import FeedbackDialog from "@/components/dialogs/FeedbackDialog.vue";
import Breadcrumbs from "@/components/Breadcrumbs.vue";

const route = useRoute();
const coursesStore = useCoursesStore();
const moduleInfo = ref();
const moduleResources = ref();
const completeness = ref({
  articles: 0,
  homework: 0,
  test: 0,
  total: 0
});

coursesStore.userCourses[route.params.id].modules[route.params.mid].then(res => {
  moduleInfo.value = res;
  return res.resources;
}).then(res => {
  moduleResources.value = res;
  let maxPoints = 0;
  Promise.all([
    res.articles.value.then(r => {
      const completedChapters = r.reduce((acc, item) => {
        return (acc + (+item.completed.value));
      }, 0);

      maxPoints += r.length;
      completeness.value.articles = completedChapters / r.length * 100;
      completeness.value.total += completedChapters;
    }),

    res.homework.value.status.then(r => {
      maxPoints += 1;
      if (r === "Done") {
        completeness.value.homework = 1;
        completeness.value.total += 1;
      }
    }),

    res.test.value.review.then(r => {
      maxPoints += 1;
      if (r?.passed) {
        completeness.value.test = 1;
        completeness.value.total += 1;
      }
    })]).then(_ => {
    completeness.value.total = completeness.value.total / maxPoints * 100;
  });

});

const showFeedbackDialog = ref(false);

onMounted(() => {
  window.scrollTo({
    top: 0,
    left: 0,
    behavior: 'smooth'
  });
});
</script>

<template>
  <div class="mb-5">
    <div class="mt-5">
      <Breadcrumbs></Breadcrumbs>
    </div>

    <!-- TODO: адаптировать под экраны иконки законченности задания -->
    <!-- TODO: данные не совпадают с макетом, не времени выполнения каждой секции -->
    <div class="task-row">
      <div class="task-column">
        <div class="title task-title">{{ moduleInfo.name }}</div>
        <v-progress-circular color="#6FCF97"
                             :model-value="completeness.total"
                             :size="50" :width="10"/>
      </div>
      <div class="time-text">~{{ formatLongEstimate(moduleInfo?.estimatedTime?.total) }}</div>
    </div>

    <div class="section-container timeline">
      <router-link :to="{name: 'articles', params: { id: route.params.id, mid: route.params.mid }}"
                   :class="'link-none ' + ((completeness.articles === 100) ? ('done') : (''))">
        <div class="task-row">
          <div class="task-column">
            <div class="article-text task-title">
              <div class="materials"></div>
              Материалы
            </div>
            <div v-if="completeness.articles === 100" class="completeness-marker complete"></div>
            <v-progress-circular v-else color="#6FCF97" :model-value="completeness.articles" :size="50" :width="10"/>
          </div>
          <div class="time-text">~{{ formatLongEstimate(moduleInfo?.estimatedTime?.articles) }}</div>
        </div>
      </router-link>

      <router-link :to="{name: 'homework', params: { id: route.params.id, mid: route.params.mid }}"
                   :class="'link-none ' + ((moduleResources?.homework?.status === 'Done') ? ('done') : (''))">
        <div class="task-row">
          <div class="task-column">
            <div class="article-text task-title">
              <div class="homeworks"></div>
              Домашнее задание
            </div>
            <div
                :class="'completeness-marker ' + ((moduleResources?.homework?.status === 'Done') ? ('complete') : ('uncompleted'))"></div>
          </div>
          <div class="time-text">~{{ formatLongEstimate(moduleInfo?.estimatedTime?.homework) }}</div>
        </div>
      </router-link>

      <div class="task-row clickable no-timeline" @click="showFeedbackDialog = true">
        <div class="task-column">
          <div class="article-text task-title">
            <div class="feedback"></div>
            Обратная связь по уроку
          </div>
        </div>
        <div class="time-text">Помогите нам стать еще лучше!</div>
      </div>


      <router-link :to="{name: 'test', params: { id: route.params.id, mid: route.params.mid }}"
                   :class="'link-none ' + ((moduleResources?.test?.review?.passed) ? ('done') : ('')) ">
        <div class="task-row">
          <div class="task-column">
            <div class="article-text task-title">
              <div class="tests"></div>
              Тест по теме
            </div>
            <div
                v-if="(!(moduleResources?.test?.review?.passed)) && (moduleResources?.test?.currentTry > moduleResources?.test?.triesLimit)"
                class="completeness-marker failed">
            </div>
            <div v-else
                 :class="'completeness-marker ' + ((moduleResources?.test?.review?.passed) ? ('complete') : ('uncompleted'))">
            </div>
          </div>
          <div class="time-text">~{{ formatLongEstimate(moduleInfo?.estimatedTime?.test) }}</div>
        </div>
      </router-link>
    </div>
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
  padding-right: 30px;
  box-sizing: border-box;
}

.task-column {
  display: flex;
  align-items: center;
  gap: 20px;
  justify-content: space-between;
}

.task-title {
  min-width: max(50%, 300px);
}

.section-container > * {
  background-color: rgb(246, 248, 249);
  padding: 30px 0 30px 80px;
  border-radius: 5px;
  transition: background-color 0.3s ease;
}

.section-container > *:hover {
  background-color: rgb(226 226 226);
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

.completeness-marker {
  background-position: center;
  background-repeat: no-repeat;
  background-size: 100%;
  width: 50px;
  height: 50px;
  margin-bottom: -40px;
}

.completeness-marker.complete {
  background-image: url(/src/assets/images/task-complete.png);
  transform: scale(1.2);
}

.completeness-marker.uncompleted {
  background-image: url("../assets/images/task-uncompleted.png");
}

.completeness-marker.failed {
  background-image: url("../assets/images/red-sign.svg");
  filter: drop-shadow(0px 0px 3px #fd5252);
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

.section-container {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.v-progress-circular {
  margin-bottom: -30px;
}
</style>