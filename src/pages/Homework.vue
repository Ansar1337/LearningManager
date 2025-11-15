<script setup>

import Breadcrumbs from "@/components/Breadcrumbs.vue";
import {onMounted, ref} from "vue";
import {useCoursesStore} from "@/stores/CoursesStore.js";
import {useRoute} from "vue-router";
import {useUserStore} from "@/stores/UserStore.js";
import {formatDatetime} from "@/helpers/Formatters.js";

let coursesStore = useCoursesStore();
let userStore = useUserStore();
let route = useRoute();

onMounted(() => {
  window.scrollTo({
    top: 0,
    left: 0,
    behavior: 'smooth'
  });
});

let module = ref();
let homework = ref();
let userName = ref(userStore?.session?.userName);
let userId = ref(userStore?.session?.userId);
let message = defineModel('message', {default: ""})

coursesStore.userCourses[route.params.id].modules[route.params.mid].then(result => module.value = result);
coursesStore.userCourses[route.params.id].modules[route.params.mid].resources.homework.then(result => homework.value = result);

function uploadSubmission(event) {
  let file = event?.target?.files?.[0];
  if (file) {
    homework.value?.tools?.uploadHomework(file);
  }
}

function downloadFile(hash) {
  if (hash) {
    homework.value?.tools?.downloadHomework(hash);
  }
}

function addComment(e) {
  if (message.value) {
    homework.value?.tools?.addComment(message.value).then(_ => {
      const messageContainer = document.getElementById("messages-container");
      messageContainer.scrollTo({
        top: messageContainer.scrollHeight,
        left: 0,
        behavior: 'smooth'
      })
    });
    message.value = "";
  }
}
</script>

<template>
  <div class="mb-5">
    <div class="mt-5">
      <Breadcrumbs></Breadcrumbs>
    </div>
    <div class="title">
      <div>{{ module?.name }}</div>
      <div class="homework-subtitle">
        Homework
      </div>
    </div>

    <div v-html="homework?.task"></div>
    <div class="subtitle">
      Submitting the task
    </div>
    <div class="submitted">
      <div v-for="submission in homework?.submissions" class="submitted-container"
           @click="() => downloadFile(submission?.hash)">
        <div>
          <div class="submitted-filename">
            {{ submission?.fileName }}
          </div>
          <div class="submitted-time">
            {{ formatDatetime(submission?.date) }}
          </div>
        </div>
        <div>
          {{ submission?.status }}
        </div>
      </div>
    </div>
    <div @click="$refs.file.click()" class="title-file-input">
      <input type="file" ref="file" style="display: none" @change="uploadSubmission"/>
      <div class="file-input-text">Upload File</div>
      <div class="icon-downward"></div>
    </div>
    <div class="subtitle">
      Chat with the Instructor
    </div>
    <div class="chat">
      <div class="messages-container" id="messages-container">
        <div v-for="(message, index) in homework?.comments" :key="index" class="message-container"
             :class="{'message-container-right':message?.senderId === userId}">
          <div class="username">
            {{ message?.senderName }}
          </div>
          <div class="message-time">
            {{ formatDatetime(message?.dateTime) }}
          </div>
          <div class="message-text"
               :class="{'message-text-right': message?.senderId === userId, 'message-text-left': message?.senderId !== userId}">
            {{ message?.message }}
          </div>
        </div>
      </div>
      <div class="message-send">
        <div class="message-input">
          <textarea v-model="message" placeholder="Your message" class="textarea"></textarea>
        </div>
        <div>
          <button @click="addComment" class="send bg-summer-sky text-white">Send</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.textarea {
  border: 1px solid gray;
  border-radius: 5px;
  margin-right: 10px;
  padding-left: 7px;
  height: 20px;
  width: 100%;
  min-height: 30px;
}

.submitted-container {
  display: flex;
  gap: 20px;
  align-items: center;
  cursor: pointer;
}

.submitted-filename {
  font-size: 16px;
  font-weight: 600;
  line-height: 21px;
  letter-spacing: 0;
}

.submitted-time {
  font-size: 14px;
  font-weight: 500;
  line-height: 19px;
  letter-spacing: 0;
  color: #828282;
}

.messages-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-height: 300px;
  overflow-y: scroll;
  padding-right: 20px;
}

.message-container {
  display: flex;
  flex-direction: column;
}

.message-container-right {
  align-items: end;
}

.message-send {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.message-input {
  flex-grow: 1;
  min-width: 300px;
}

.send {
  border-radius: 5px;
  padding: 2px 20px 2px 20px;
  font-size: 16px;
  line-height: 20px;
  height: 30px;
}

.chat {
  display: flex;
  flex-direction: column;
  gap: 20px;
  border: 1px solid #E0E0E0;
  padding: 20px;
  border-radius: 6px;
}

.username {
  font-size: 16px;
  font-weight: 600;
  line-height: 21px;
  letter-spacing: 0;
}

.message-time {
  font-size: 14px;
  font-weight: 500;
  line-height: 19px;
  letter-spacing: 0;
  color: #828282;
}

.message-text {
  font-size: 16px;
  font-weight: 500;
  line-height: 21px;
  letter-spacing: 0;
  background-color: #E5F5FF;
  /* TODO: подобрать оптимальную ширину сообщения */
  width: 40%;
  padding: 20px;
}

.message-text-right {
  border-radius: 8px 0 8px 8px;
}

.message-text-left {
  border-radius: 0 8px 8px 8px;
}

.title {
  font-size: 36px;
  font-weight: 600;
  line-height: 44px;
  letter-spacing: 0;
}

.homework-subtitle {
  font-size: 22px;
  font-weight: 500;
  line-height: 27px;
  letter-spacing: 0;
  color: #828282;
}

.subtitle {
  font-size: 24px;
  font-weight: 600;
  line-height: 32px;
  letter-spacing: 0;
}

.title-file-input {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
  padding: 10px;
  border: 4px dashed #BDBDBD;
  border-radius: 6px;
  cursor: pointer;
}

.file-input-text {
  font-size: 20px;
  font-weight: 600;
  line-height: 27px;
  letter-spacing: 0;
  color: #828282;
}

.icon-downward {
  background-image: url("@/assets/images/arrow-downward.png");
  background-repeat: no-repeat;
  background-size: 100% 80%;
  width: 31px;
  height: 46px;
}
</style>