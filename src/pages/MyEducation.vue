<script setup>
import {useCoursesStore} from "@/stores/CoursesStore.js";

const coursesStore = useCoursesStore();

</script>

<template>
  <pre>{{ coursesStore.userCourses }}</pre>
</template>

<style scoped>

</style>