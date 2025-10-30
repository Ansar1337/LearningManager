<script setup>
const isOpen = defineModel("isOpen", {default: false});
</script>

<template>
  <v-dialog v-model="isOpen" class="dialog-size">
    <div class="card">
      <div class="card-title">
        Мы будем благодарны обратной связи
      </div>
      <div class="card-text rating-container">
        <div>Качество материала</div>

        <div class="rating-area">
          <input type="radio" id="star-5" name="rating" value="5">
          <label for="star-5" title="Оценка «5»"></label>
          <input type="radio" id="star-4" name="rating" value="4">
          <label for="star-4" title="Оценка «4»"></label>
          <input type="radio" id="star-3" name="rating" value="3">
          <label for="star-3" title="Оценка «3»"></label>
          <input type="radio" id="star-2" name="rating" value="2">
          <label for="star-2" title="Оценка «2»"></label>
          <input type="radio" id="star-1" name="rating" value="1">
          <label for="star-1" title="Оценка «1»"></label>
        </div>
      </div>
      <div class="card-text">
        Комментарий:
      </div>
      <div>
        <textarea class="textarea"/>
      </div>
      <div class="btn-container">
        <v-btn color="#2D9CDB" class="action-button mb-2 text-none" elevation="0" @click="isOpen = false">
          Отправить
        </v-btn>
      </div>
    </div>
  </v-dialog>
</template>

<style scoped>
.rating-container {
  display: flex;
  align-items: center;
}

.textarea {
  background-color: #E0E0E0;
  width: 100%;
  min-height: 100px;
  border-radius: 6px;
  padding: 8px;
}

.card {
  background-color: white;
  border-radius: 6px;
  padding: 20px;
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

.card-text {
  font-size: 18px;
  font-weight: 400;
  line-height: 27px;
  letter-spacing: 0;
}

.dialog-size {
  min-width: 300px;
  max-width: 600px;
}

.rating-area {
  overflow: hidden;
  width: 265px;
  margin: 0 auto;
}

.rating-area:not(:checked) > input {
  display: none;
}

.rating-area:not(:checked) > label {
  float: right;
  width: 42px;
  padding: 0;
  cursor: pointer;
  font-size: 32px;
  line-height: 32px;
  color: lightgrey;
  text-shadow: 1px 1px #bbb;
}

.rating-area:not(:checked) > label:before {
  content: '★';
}

.rating-area > input:checked ~ label {
  color: gold;
  text-shadow: 1px 1px #c60;
}

.rating-area:not(:checked) > label:hover,
.rating-area:not(:checked) > label:hover ~ label {
  color: gold;
}

.rating-area > input:checked + label:hover,
.rating-area > input:checked + label:hover ~ label,
.rating-area > input:checked ~ label:hover,
.rating-area > input:checked ~ label:hover ~ label,
.rating-area > label:hover ~ input:checked ~ label {
  color: gold;
  text-shadow: 1px 1px goldenrod;
}

.rate-area > label:active {
  position: relative;
}
</style>