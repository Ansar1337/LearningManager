<script setup>

import {onMounted, ref} from "vue";

const props = defineProps({
  description: String,
  actor: String,
  action: String,
  passedData: Array,
  runWith: Function,
  result: Object
})

const passedDataCopy = ref([]);

onMounted(() => {
  passedDataCopy.value = JSON.parse(JSON.stringify(props.passedData));
});

</script>

<template>
  <h2><code>{{ actor }}</code> / <code>{{ action }}</code></h2>
  <section class="test-container">
    <section class="test">
      <fieldset>
        <legend>{{ description }}</legend>
        <label v-if="passedDataCopy.length" v-for="datum in passedDataCopy" :key="datum.name">
          {{ datum.name }}:
          <input :type="datum.type" v-model=datum.value>
        </label>
        <span v-else>Входящие данные не требуются</span>

        <button @click="runWith(...passedDataCopy.map(datum => datum.value))">Тест</button>
      </fieldset>
      <fieldset>
        <legend>Результат</legend>
        <pre>{{ result || "Нет данных" }}</pre>
      </fieldset>
    </section>
  </section>
  <hr>
</template>

<style scoped>

h2 {
  padding-left: 10px;
}

.test-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-self: flex-start;
}

.test {
  display: flex;
  width: 100%;
  justify-content: space-between;
}

.test fieldset {
  min-width: 400px;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: center;
}

input, button, textarea {
  border: 1px solid black;
  padding: 5px;
  border-radius: 5px;
}

button {
  background-color: #faf7f7;
}

button:hover {
  background-color: #BDBDBD;
}

button:active {
  background-color: #83919F;
}

fieldset {
  border: 1px solid #e2e2e2;
  border-radius: 10px;
  padding: 20px;
}

pre {
  font-size: 13px;
}
</style>