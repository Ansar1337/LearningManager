<script setup>
import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";
import {useUserStore} from "@/stores/UserStore.js";
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {onMounted, ref} from "vue";

const userStore = useUserStore();
const coursesStore = useCoursesStore();
const sessionIsReady = ref(false);

onMounted(() => {
  userStore.sessionTools.addChangingListener(coursesStore.update);
  userStore.sessionTools.loadSessionState().then(_ => {
        sessionIsReady.value = true;
      }
  );
});

</script>

<template>
  <div class="main" v-if="sessionIsReady">
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
  display: flex;
  max-width: min(1920px, 85%);
  width: 100%;
  flex-direction: column;
  gap: 40px;
  align-items: center;
}

.main, .main-content > * {
  width: 100%;
}

.main-content {
  flex-grow: 1;
}

.main {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

@media only screen and (max-width: 758px) {
  .main-content {
    box-sizing: content-box;
  }
}


</style>