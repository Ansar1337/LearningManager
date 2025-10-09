<script setup>
import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";
</script>

<template>
  <v-container fluid>
    <Header class="header-content"></Header>
    <hr class="hr-line"/>
    <router-view class="main-content"></router-view>
    <Footer class="footer-content"></Footer>
  </v-container>
</template>

<style>
.header-content {
}

.hr-line {
  border: 0;
  background: #E0E0E0;
  height: 1px;
}

.main-content {
  padding: 0 40px;
  flex-direction: column;
  align-items: center;
  gap: 40px;
}

.footer-content {
  gap: 10px;
}

.header-content, .main-content, .footer-content {
  max-width: min(1920px, 85%);
  margin: 0 auto;
  display: flex;
}

.main-content > * {
  width: 100%;
}
</style>