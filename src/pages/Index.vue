<script setup>
import {UseCoursesStore} from "@/stores/CoursesStore.js";

const courses = UseCoursesStore();
</script>

<template>
  <div>
    <div class="logo">
      <div class="logo-title">
        <p>AnsarCodes —</p>
        <p>opening up prospects</p>
        <p>through education and</p>
        <p>training in IT</p>
      </div>
      <section class="logo-phone"></section>
    </div>

    <h2 class="content-study">Areas of study</h2>

    <div class="cards-container">
      <v-card v-for="course in courses.availableCourses" :key="course.id" elevation="4" class="card">
        <v-card-title>{{ course.title }}</v-card-title>
        <v-card-text>{{ course.description }}</v-card-text>
      </v-card>
    </div>

    Feedback

  </div>
</template>

<style scoped>

.cards-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
}

.cards-container .card {

}

.logo {
  height: 428px;
  position: relative;
  z-index: -1;
  border-radius: 0 0 16px 16px;
  background: linear-gradient(180deg, #6B31E4 0%, rgba(42, 120, 236, 0.8) 100%);
}

.logo-title {
  font-family: Montserrat, sans-serif;
  font-size: 36px;
  font-weight: 700;
  line-height: 44px;
  letter-spacing: 0;
  text-align: left;
  color: #FFFFFF;
  position: absolute;
  top: 114px;
  margin-left: 60px;
  z-index: 1;
}

.logo-phone {
  z-index: -1;
  width: 208px;
  height: 360px;
  position: absolute;
  top: 133px;
  right: min(120px, calc(10% + 30px));
  transform: rotate(11deg);
  background: #56CCF2;
  box-shadow: 30px 0 0 #2D9CDB;
}


.content-study {
  font-family: Montserrat, sans-serif;
  font-size: 40px;
  font-weight: 600;
  line-height: 49px;
  letter-spacing: 0;
  text-align: left;
}

.v-card {
  background: linear-gradient(180deg, #FFFFFF 0%, rgba(243, 251, 255, 0.8) 100%);
}
</style>