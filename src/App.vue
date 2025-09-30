<script setup>
import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";
</script>

<template>
  <v-container fluid>
    <Header class="header-content"></Header>
    <router-view class="main-content"></router-view>
    <Footer class="footer-content"></Footer>
  </v-container>
</template>

<style>
.header-content {

}

.main-content {
  padding: 0 40px;
  flex-direction: column;
  align-items: center;
  gap: 80px;
}

.footer-content {
  gap: 10px;
}

.header-content, .main-content, .footer-content {
  max-width: 1000px;
  margin: 0 auto;
  display: flex;
}

.main-content > * {
  width: 100%;
}
</style>