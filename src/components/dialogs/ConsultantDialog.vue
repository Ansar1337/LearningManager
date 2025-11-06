<script setup>
const isOpen = defineModel("isOpen", {default: false});
</script>

<template>
  <v-dialog v-model="isOpen" class="dialog-size">
    <div class="card">
      <div class="card-title">
        Спасибо, наш консультант скоро с вами свяжется
      </div>
      <div class="btn-container">
        <v-btn color="#2D9CDB" class="action-button mb-2 text-none" elevation="0" @click="isOpen = false">
          Продолжить
        </v-btn>
      </div>
    </div>
  </v-dialog>
</template>

<style scoped>
.card {
  background-color: white;
  border-radius: 6px;
  padding: 20px;
  min-height: 300px;
  display: flex;
  justify-content: space-between;
  flex-direction: column;
}

.action-button {
  font-size: 16px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0;
}

.btn-container {
  text-align: right;
}

.card-title {
  font-size: 22px;
  font-weight: 600;
  line-height: 33px;
  letter-spacing: 0;
}

.dialog-size {
  min-width: 300px;
  max-width: 600px;
}
</style>