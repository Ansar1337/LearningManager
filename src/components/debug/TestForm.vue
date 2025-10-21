<script setup>

import {onMounted, ref} from "vue";

const props = defineProps({
  description: String,
  actor: String,
  action: String,
  passedData: Array,
  runWith: Function
})

const result = ref(null);
const inputsData = ref([]);
const passedDataCopy = ref([]);

onMounted(() => {
  passedDataCopy.value = JSON.parse(JSON.stringify(props.passedData));

  passedDataCopy.value.forEach(datum => {
    switch (datum?.type) {
      case "checkbox": {
        inputsData.value.push(datum?.checked);
        break;
      }

      case "file": {
        inputsData.value.push(ref({}));
        break;
      }

      default: {
        inputsData.value.push(datum?.value);
      }
    }
  });
});

function runTest() {
  const payload = inputsData.value.map((item) => {
    let obj = item?.value ?? item;
    if (Array.isArray(obj)) {
      obj = obj[0];
    }

    if ((obj instanceof HTMLElement) && (obj?.type === "file")) {
      return obj.files[0];
    }

    return obj;
  });

  props.runWith(...payload).then(res => {
    result.value = res;
  });
}

</script>

<template>
  <div>
    <h2><code>{{ actor }}</code> / <code>{{ action }}</code></h2>
    <section class="test-container">
      <section class="test">
        <fieldset>
          <legend>{{ description }}</legend>
          <label v-if="passedDataCopy.length" v-for="(datum, index) in passedDataCopy" :key="datum.name">
            {{ datum.name }}:
            <input v-if="datum.type === 'checkbox'" :type="datum.type" v-model="inputsData[index]">
            <input v-else-if="datum.type === 'file'" :type="datum.type" :ref="inputsData[index]">
            <input v-else :type="datum.type" v-model="inputsData[index]">
          </label>

          <span v-else>Входящие данные не требуются</span>

          <button @click="runTest()">Тест</button>
        </fieldset>
        <fieldset>
          <legend>Результат</legend>
          <pre>{{ result || "Нет данных" }}</pre>
        </fieldset>
      </section>
    </section>
    <br>
    <hr>
  </div>
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
  white-space: pre-wrap;
}
</style>