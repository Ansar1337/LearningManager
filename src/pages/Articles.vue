<script setup>

import Breadcrumbs from "@/components/Breadcrumbs.vue";
</script>

<template>
  <div class="mb-5">
    <div class="mt-5">
      <Breadcrumbs></Breadcrumbs>
    </div>
    <div>Здесь будут статьи</div>
  </div>
</template>

<style scoped>
.arrow-forward {
  width: 34px;
  height: 15px;
  background-image: url("@/assets/images/arrow-forward.png");
  background-repeat: no-repeat;
  background-size: 100%;
  display: inline-block;
}

.forward {
  font-size: 24px;
  font-weight: 600;
  line-height: 29px;
  color: #828282;
}
</style>