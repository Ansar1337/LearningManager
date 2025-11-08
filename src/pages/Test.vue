<script setup>
import Breadcrumbs from "@/components/Breadcrumbs.vue";
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {useRoute} from "vue-router";
import {ref} from "vue";

const coursesStore = useCoursesStore();
const route = useRoute();

const tab = defineModel('tab', {default: 1});
const questionIndex = defineModel('question', {default: 0});
const modules = ref();
const module = ref();
const testInfo = ref();
const review = ref();

coursesStore.userCourses[route.params.id].modules.then(result => {
  modules.value = result;
  module.value = result[route.params.mid];
  return module.value.resources.test;
}).then(
    currentModuleTest => {
      testInfo.value = currentModuleTest;
      return currentModuleTest.review.value
    }).then(
    currentTestReview => {
      review.value = currentTestReview;
      if (review.value.passed) {
        tab.value = 3;
      }
    });


function startTest() {
  testInfo.value.tools.launch()
      .then(() => testInfo.value.questions)
      .then(_ => {
        questionIndex.value =
            (testInfo.value.lastQuestion < testInfo.value.questionsCount)
                ? testInfo.value.lastQuestion
                : testInfo.value.questionsCount - 1;
        tab.value++;
      });
}

/* TODO: перезапуск квеста ведет к ошибке */

</script>

<template>
  <div class="mb-5">
    <div class="mt-5">
      <Breadcrumbs></Breadcrumbs>
    </div>

    <v-window v-model="tab">
      <v-window-item :value="1">
        <div class="card">
          <div class="title">
            Тест по теме "{{ module?.name }}"
          </div>
          <div class="info">
            <div class="test-icon"></div>
            <div>Для зачёта необходимо {{ testInfo ? testInfo.questionsCount - testInfo.mistakesLimit : 'x' }}
              правильных ответа
            </div>
          </div>
          <div class="info">
            <div class="test-icon"></div>
            <div>Вопросов: {{ testInfo?.questionsCount }}</div>
          </div>
          <div class="info">
            <div class="test-icon"></div>
            <div>Максимум попыток: {{ testInfo?.triesLimit }}</div>
          </div>
          <div class="info">
            <div class="test-icon"></div>
            <div>Текущая попытка: {{ testInfo?.currentTry }}
              {{ (testInfo?.currentTry === testInfo?.triesLimit ? "(последняя)" : "") }}
            </div>
          </div>
          <div>
            <v-btn v-if="testInfo?.currentTry <= testInfo?.triesLimit" @click="startTest"
                   :disabled="!testInfo" color="#2D9CDB"
                   class="start-btn bg-summer-sky text-white mt-2 text-none" elevation="0">
              {{ testInfo?.state === "in_progress" ? `Продолжить с вопроса №${testInfo?.lastQuestion + 1}` : "Начать" }}
            </v-btn>
          </div>
        </div>
      </v-window-item>

      <v-window-item :value="2">
        <v-window v-model="questionIndex">
          <template v-for="(question, index) in testInfo.questions || []">
            <v-window-item :value="index">
              <div class="card">
                <div class="title">
                  {{ question?.title }}
                </div>
                <section class="questions">
                  <div v-for="(_, key) in question?.options" :key="key" class="info">
                    <label class="custom-checkbox-label">
                      <input v-if="question.type === 'single'"
                             :name="question.title"
                             v-model="question.singleStorage"
                             :value="key"
                             type="radio"
                             class="custom-checkbox">
                      <input v-else
                             v-model="question.options[key]"
                             :checked="question.options[key]"
                             type="checkbox"
                             class="custom-checkbox">
                      {{ key }}
                    </label>
                  </div>
                </section>
                <div>
                  <v-btn
                      @click="(index === testInfo?.questions?.length - 1) ? (testInfo?.tools?.finish() && tab++) : questionIndex++"
                      color="#2D9CDB" class="start-btn bg-summer-sky text-white mt-2 text-none" elevation="0">
                    {{ index === testInfo?.questions?.length - 1 ? 'Завершить' : 'Далее' }}
                  </v-btn>
                </div>
              </div>
            </v-window-item>
          </template>
        </v-window>

        <div class="pagination">
          <v-btn @click="questionIndex--" :disabled="questionIndex === 0" color="#2D9CDB"
                 class="pagination-btn bg-summer-sky text-white mt-2 text-none" elevation="0">
            <
          </v-btn>
          <template v-for="(_, index) in testInfo?.questions || []">
            <v-btn @click="questionIndex = index" v-if="questionIndex !== index"
                   class="pagination-btn border-summer-sky text-summer-sky mt-2 text-none" elevation="0">
              <span class="text-summer-sky">{{ index + 1 }}</span>
            </v-btn>
            <v-btn @click="questionIndex = index" v-else color="#2D9CDB"
                   class="pagination-btn bg-summer-sky text-white mt-2 text-none" elevation="0">
              {{ index + 1 }}
            </v-btn>
          </template>
          <v-btn @click="questionIndex++" :disabled="questionIndex === testInfo?.questions?.length - 1" color="#2D9CDB"
                 class="pagination-btn bg-summer-sky text-white mt-2 text-none" elevation="0">
            >
          </v-btn>
        </div>
      </v-window-item>

      <v-window-item :value="3">
        <div class="card">
          <div class="title">
            Тест по теме "{{ module?.name }}"
          </div>
          <div v-if="!testInfo?.review?.passed" class="info review">
            Unfortunately, the test was not passed. Try returning to this test after reviewing the material.
          </div>
          <div v-else class="info review">
            Congratulations, the test has been passed!
          </div>
          <div class="card-content-completeness">
            <div class="card-content-completeness-progress">
              <v-progress-linear color="#6FCF97" :model-value="Number(testInfo?.review?.score)" :height="7"
                                 rounded="2"></v-progress-linear>
            </div>
            <div class="percent" :style="{color: testInfo?.review?.passed ? '#6FCF97' : '#EB5757'}">
              {{ testInfo?.review?.structure?.length - testInfo?.review?.mistakes }}
              /
              {{ testInfo?.review?.structure?.length }}
            </div>
          </div>
          <div>
            <v-btn v-if="(testInfo?.review?.passed) && (modules.length-1 > route.params.mid)"
                   :to="{name: 'module', params: { id: route.params.id, mid: +route.params.mid+1 }}"
                   color="#2D9CDB"
                   class="start-btn bg-summer-sky text-white mt-2 text-none" elevation="0">
              К следующему модулю
            </v-btn>
            <v-btn v-else-if="(testInfo?.review?.passed)"
                   :to="{name: 'coursePage', params: { id: route.params.id }}"
                   color="#2D9CDB"
                   class="start-btn bg-summer-sky text-white mt-2 text-none" elevation="0">
              На страницу курса
            </v-btn>
            <v-btn v-else :to="{name: 'module', params: { id: route.params.id, mid: route.params.mid }}"
                   color="#2D9CDB"
                   class="start-btn bg-summer-sky text-white mt-2 text-none" elevation="0">
              Вернуться в модуль
            </v-btn>
          </div>
        </div>
      </v-window-item>
    </v-window>
  </div>
</template>

<style scoped>
.border-summer-sky {
  border: 1px solid #2D9CDB;
}

.pagination-btn {
  border-radius: 5px;
  font-size: 16px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0;
  width: 40px;
  min-width: 40px;
}

.pagination {
  display: flex;
  gap: 10px;
  padding-left: 20px;
  flex-wrap: wrap;
}

.custom-checkbox {
  width: 17px;
  height: 17px;
}

.info {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  align-items: center;
}

.info.review {
  font-size: 20px;
}

.test-icon {
  width: 28px;
  height: 28px;
  background-image: url("../assets/images/test-tick.png");
  background-repeat: no-repeat;
  background-size: 100%;
}

.card {
  background-color: #F6F8F9;
  padding: 20px;
  border-radius: 6px;
  display: flex;
  flex-wrap: wrap;
  flex-direction: column;
  gap: 20px;
  min-height: 250px;
}

.card .questions {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 20px;
}

.start-btn {
  border-radius: 5px;
  padding: 2px 20px 2px 20px;
  font-size: 16px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0;
}

.title {
  font-size: 24px;
  font-weight: 600;
  line-height: 29px;
  letter-spacing: 0;
}

.card-content-completeness {
  display: flex;
  gap: 20px;
  align-items: center;
}

.card-content-completeness-progress {
  flex-grow: 1;
}

.percent {
  font-size: 24px;
  font-weight: 700;
  line-height: 29px;
  letter-spacing: 0;
}
</style>