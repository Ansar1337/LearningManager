<script setup>
import Breadcrumbs from "@/components/Breadcrumbs.vue";
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {useRoute} from "vue-router";
import {ref} from "vue";

const coursesStore = useCoursesStore();
const route = useRoute();

let tab = defineModel('tab', {default: 1});
let questionIndex = defineModel('question', {default: 0});
let module = ref();
let testInfo = ref();

coursesStore.userCourses[route.params.id].modules[route.params.mid].resources.test.then(result => {
  module.value = coursesStore.userCourses[route.params.id].modules[route.params.mid];
  testInfo.value = result;
});

/* TODO: перезапуск квеста ведет к ошибке */
function startTest() {
  testInfo.value?.tools?.launch().then(_ => {
    testInfo.value.questions.then(_ => {
      tab.value = 2;
    })
  })
}

function complete() {
  tab.value = 3;
  testInfo.value?.tools?.finish();
}

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
            <div>{{ testInfo?.questionsCount }} вопросов</div>
          </div>
          <div class="info">
            <div class="test-icon"></div>
            <div>{{ testInfo?.triesLimit }} попытки</div>
          </div>
          <div>
            <v-btn @click="startTest" :disabled="!testInfo" color="#2D9CDB"
                   class="start-btn bg-summer-sky text-white mt-2 text-none" elevation="0">
              Начать!
            </v-btn>
          </div>
        </div>
      </v-window-item>

      <v-window-item :value="2">
        <v-window v-model="questionIndex">
          <template v-for="(question, index) in testInfo?.questions || []">
            <v-window-item :value="index">
              <div class="card">
                <div class="title">
                  {{ question?.title }}
                </div>
                <div v-for="(value, key) in question?.options" class="info">
                  <input :checked="value" type="checkbox" class="custom-checkbox"
                         @change="event => question.options[key] = event.target.checked">
                  <label class="custom-checkbox-label">{{ key }}</label>
                </div>
                <div>
                  <v-btn @click="index === testInfo?.questions?.length - 1 ? complete() : questionIndex++"
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
          <div v-if="!testInfo?.review?.passed" class="info">
            Unfortunately, the test was not passed. Try returning to this test after reviewing the material.
          </div>
          <div v-else class="info">
            Congratulation, test passed!
          </div>
          <div class="card-content-completeness">
            <div class="card-content-completeness-progress">
              <v-progress-linear color="#6FCF97"
                                 :model-value="Number(testInfo?.review?.mistakes / testInfo?.review?.structure?.length * 100)"
                                 :height="7"
                                 rounded="2"></v-progress-linear>
            </div>
            <div class="percent" :style="{color: testInfo?.review?.passed ? '#6FCF97' : '#EB5757'}">
              {{ testInfo?.review?.structure?.length - testInfo?.review?.mistakes }}
              /
              {{ testInfo?.review?.structure?.length }}
            </div>
          </div>
          <div>
            <v-btn :to="{name: 'module', params: { id: route.params.id, mid: route.params.mid }}" color="#2D9CDB"
                   class="start-btn bg-summer-sky text-white mt-2 text-none" elevation="0">
              Вернуться к курсу
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