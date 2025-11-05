<script setup>

</script>

<template>
  <div class="mb-5">
    <div class="mt-5">
      <!-- TODO: breadcrumbs -->
      <router-link to="/" class="link-none">
        <div class="link-none forward">
          Моё обучение
          <div class="arrow-forward"></div>
          Java
          <div class="arrow-forward"></div>
          Основы Java
        </div>
      </router-link>
    </div>
    <div>Здесь будет домашнее задание</div>
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