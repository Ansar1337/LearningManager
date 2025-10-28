<script setup>
import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";
import {useUserStore} from "@/stores/UserStore.js";
import {useCoursesStore} from "@/stores/CoursesStore.js";

const userStore = useUserStore();
const coursesStore = useCoursesStore();

userStore.sessionTools.addChangingListener(coursesStore.update);

</script>

<template>
  <div class="main">
    <Header class="header-content"></Header>
    <hr class="hr-line"/>
    <router-view class="main-content"></router-view>
    <Footer class="footer-content"></Footer>
  </div>
</template>

<style>
.hr-line {
  border: 0;
  background: #E0E0E0;
  height: 1px;
}

.main-content {
  margin: 0 auto;
  /* padding: 0 40px; */
  display: flex;
  max-width: min(1920px, 85%);
  flex-direction: column;
  gap: 40px;
  align-items: center;
}

.main, .main-content > * {
  width: 100%;
}

@media only screen and (max-width: 758px) {
  .main-content {
    box-sizing: content-box;
  }
}

</style>