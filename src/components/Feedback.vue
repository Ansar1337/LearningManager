<script setup>

</script>

<template>
  <section class="feedback bg-alice-blue mb-5">
    <div class="feedback-title text-summer-sky mt-6">Остались вопросы?</div>
    <div class="feedback-text">Оставьте контакты — мы с вами свяжемся</div>
    <form class="mt-6">
      <input type="text" placeholder="Имя" class="input mb-6">
      <input type="text" placeholder="+7 (777) 777-77-77" class="input mb-6">
      <input type="text" placeholder="Электронная почта" class="input mb-6">
      <button type="submit" class="confirm bg-summer-sky text-white mb-6">Получить консультацию</button>
    </form>

    <!-- TODO: добавить ссылки на страницы "условия политики" и "пользовательского соглашения" -->
    <div class="feedback-small mb-6">Нажимая кнопку, принимаю условия политики и пользовательского соглашения</div>
  </section>
</template>

<style scoped>

/* feedback */
.feedback {
  border-radius: 16px;
  padding: 10px 20px 10px 20px;
  box-sizing: border-box;
}

.feedback-title {
  font-size: 40px;
  font-weight: 600;
  line-height: 49px;
}

.feedback-small {
  font-size: 12px;
  font-weight: 400;
  line-height: 15px;
}

.feedback-text {
  font-size: 18px;
  font-weight: 400;
  line-height: 22px;
}

.input {
  border: 1px solid gray;
  border-radius: 5px;
  margin-right: 10px;
  padding-left: 7px;
  height: 30px;
}

.confirm {
  border-radius: 5px;
  padding: 2px 20px 2px 20px;
  font-size: 16px;
  line-height: 20px;
  height: 30px;
}

</style>