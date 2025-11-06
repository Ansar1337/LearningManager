<script setup>
import Breadcrumbs from "@/components/Breadcrumbs.vue";
import {onMounted, ref} from "vue";
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {useRoute} from "vue-router";

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
let articles = ref();


coursesStore.userCourses[route.params.id].modules[route.params.mid].then(result => module.value = result);
coursesStore.userCourses[route.params.id].modules[route.params.mid].resources.articles.then(result => articles.value = result);
</script>

<template>
  <div class="mb-5">
    <div class="mt-5">
      <Breadcrumbs></Breadcrumbs>
    </div>

    <div class="title">
      <div>{{ module?.name }}</div>
      <div class="articles-subtitle">
        estimated completion time: ~30 minutes
      </div>
    </div>

    <div>
      Здесь будут статьи
    </div>

    <pre>{{ articles }}</pre>
  </div>
</template>

<style scoped>
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