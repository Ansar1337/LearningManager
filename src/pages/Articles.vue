<script setup>
import Breadcrumbs from "@/components/Breadcrumbs.vue";
import {onMounted, ref, watch} from "vue";
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {useRoute} from "vue-router";
import Treeview from "@/components/Treeview.vue";

onMounted(() => {
  window.scrollTo({
    top: 0,
    left: 0,
    behavior: 'smooth'
  });
});

let coursesStore = useCoursesStore();
let route = useRoute();

let module = ref();
let articles = ref([]);
let selectedArticle = ref();
let nextArticle = ref();
let nodes;

watch(selectedArticle, (newValue) => {
  newValue.completed = true;
  nextArticle = nodes[nodes.indexOf(newValue) + 1];
})

coursesStore.userCourses[route.params.id].modules[route.params.mid].then(result => module.value = result);
coursesStore.userCourses[route.params.id].modules[route.params.mid].resources.articles.then(result => {
  articles.value = result;
  nodes = [];
  collectToPlainArray(result, nodes);
  selectedArticle.value = nodes[0];
  nextArticle.value = nodes[1];
});

function collectToPlainArray(articles, result = []) {
  for (let article of articles) {
    if (article.type === 'group')
      collectToPlainArray(article.content, result);
    else
      result.push(article);
  }
}
</script>

<template>
  <div>
    <div class="articles-container">
      <div class="mt-5 treeview-container">
        <Treeview v-if="articles?.length > 0" :items="articles" v-model:selected-item="selectedArticle"/>
      </div>
      <div class="mb-5 articles-content">
        <div>
          <Breadcrumbs></Breadcrumbs>
        </div>

        <div class="title">
          <div>{{ module?.name }}</div>
          <div class="articles-subtitle">
            estimated completion time: ~30 minutes
          </div>
        </div>

        <div v-if="selectedArticle?.content?.status !== 'error'" v-html="selectedArticle?.content">
        </div>
        <div v-else>Ошибка при получении статьи</div>

        <div @click="selectedArticle = nextArticle" v-if="nextArticle" class="next-chapter">
          <div class="next-chapter-tip">Next chapter</div>
          <div class="next-chapter-title">{{ nextArticle?.name }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.next-chapter-tip {
  font-size: 14px;
  line-height: 21px;
  letter-spacing: 0;
  color: #828282;
}

.next-chapter-title {
  font-size: 20px;
  font-weight: 600;
  line-height: 29px;
  letter-spacing: 0;
  color: #2D9CDB;
  cursor: pointer;
}

.next-chapter {
  display: flex;
  flex-direction: column;
  align-items: end;
  padding-top: 20px;
}

.articles-container {
  display: flex;
  gap: 40px;
  flex-wrap: wrap;
  padding-left: 40px;
  padding-right: 40px;
}

.articles-content {
  min-width: 375px;
  max-width: min(70%, 1000px);
}

.main-content {
  max-width: 100%;
  align-items: flex-start;
}

.treeview-container {
  min-width: 150px;
}

.main-content > * {
  width: initial;
}

.title {
  font-size: 36px;
  font-weight: 600;
  line-height: 44px;
  letter-spacing: 0;
}

.articles-subtitle {
  font-size: 22px;
  font-weight: 500;
  line-height: 27px;
  letter-spacing: 0;
  color: #828282;
}
</style>