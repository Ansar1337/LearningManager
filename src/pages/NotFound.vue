<script setup>
import {useRoute} from "vue-router";

const route = useRoute();

</script>

<template>
  <div>
    PAGE [{{ route.path }}] DOES NOT EXISTS
  </div>
</template>

<style scoped>
div {
  font-size: 60px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
</style>