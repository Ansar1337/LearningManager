<script setup>
import {ref} from "vue";

let props = defineProps({
  items: Array
})

let selectedItem = defineModel('selectedItem', {default: null})

// модифицируем объект, чтобы можно было управлять состояниями списков
for (let item of props.items) {
  if (item?.type === 'group')
    item.hidden = ref(false);
}

function select(item) {
  selectedItem.value = item;
}
</script>

<template>
  <div class="treeview">
    <ul>
      <template v-for="item in props.items">
        <li v-if="item?.type === 'article'" class="child">
          <div @click="() => select(item)">{{ item?.name }}</div>
        </li>
        <li v-else :class="{ 'parent-close': item.hidden, 'parent-open': !item.hidden }">
          <div @click="item.hidden = !item.hidden">{{ item?.name }}</div>
          <Treeview v-show="!item.hidden" v-if="item?.content?.length > 0" :items="item.content"
                    v-model:selected-item="selectedItem"></Treeview>
        </li>
      </template>
    </ul>
  </div>
</template>

<style scoped>
.treeview {
  font-size: 18px;
  line-height: 20px;
  letter-spacing: 0;
  padding-left: 20px;
}

.treeview * li::marker {
  color: #828282;
}

.treeview * li {
  cursor: pointer;
  padding-top: 5px;
}

.parent-close {
  list-style-type: "⯈";
  padding-left: 5px;
}

.parent-open {
  list-style-type: "⯆";
  padding-left: 5px;
}

.child {
  list-style-type: none;
  padding-left: 5px;
}
</style>