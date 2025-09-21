<script setup>
import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";
</script>

<template>
  <v-container fluid>
    <Header></Header>
    <router-view></router-view>
    <Footer></Footer>
  </v-container>
</template>

<style scoped>

</style>