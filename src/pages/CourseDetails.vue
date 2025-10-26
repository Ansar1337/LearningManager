<script setup>

</script>

<template>
  <div>
    <div class="mt-5">
      <!-- TODO: breadcrumbs -->
      <router-link to="/" class="link-none">
        <div class="link-none forward">
          Моё обучение
          <div class="arrow-forward"></div>
          Java-разработчик
        </div>
      </router-link>
    </div>

    <div>
      <v-card elevation="0" color="#F6F8F9" class="pt-2 pb-2">
        <v-card-text>
          <div class="card-content-info">
            <div class="card-content-info-title">
              <div class="course-title">Java-разработчик</div>
              <div class="time-schedule">
                <div>9 января 2024</div>
                <div> —</div>
                <div>9 сентября 2024</div>
              </div>
            </div>

            <div class="card-content-completeness">
              <div class="card-content-completeness-progress">
                <v-progress-linear color="#6FCF97" model-value="20" :height="7" rounded="2"></v-progress-linear>
              </div>
              <div class="percent">20%</div>
            </div>

            <div class="card-content-continue">
              <div class="last-lesson">
                Вы остановились на главе <span class="text-summer-sky">Java Core</span>
              </div>
              <div>
                <v-btn color="#2D9CDB" class="start-btn bg-summer-sky text-white mt-2 text-none">
                  Продолжить обучение
                </v-btn>
              </div>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </div>

    <div class="module-title">
      Модули курса (6)
    </div>

    <div class="mb-5">
      <v-timeline density="compact" align="start">
        <v-timeline-item
            v-for="message in ['1', '2', '3']"
            :key="message.time"
            :dot-color="message.color"
            size="x-small"
        >
          <div class="mb-4">
            <div class="font-weight-normal">
              <strong>{{ message.from }}</strong> @{{ message.time }}
            </div>
            <div>{{ message.message }}</div>
          </div>
        </v-timeline-item>
      </v-timeline>
    </div>
  </div>
</template>

<style scoped>
.arrow-forward {
  width: 34px;
  height: 15px;
  background-image: url("@/assets/arrow-forward.png");
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

.course-title {
  font-size: 32px;
  font-weight: 600;
  line-height: 39px;
  letter-spacing: 0;
}

.time-schedule {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
  font-size: 24px;
  font-weight: 500;
  line-height: 29px;
  letter-spacing: 0;
}


.module-title {
  font-size: 36px;
  font-weight: 600;
  line-height: 44px;
  letter-spacing: 0;
}

.card-content-info {
  display: flex;
  gap: 12px;
  flex-direction: column;
}

.start-btn {
  border-radius: 5px;
  padding: 2px 20px 2px 20px;
  font-size: 16px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0;
}

.percent {
  font-size: 24px;
  font-weight: 700;
  line-height: 29px;
  letter-spacing: 0;
  color: #6FCF97;
}

.last-lesson {
  font-size: 22px;
  font-weight: 600;
  line-height: 27px;
  letter-spacing: 0;
}

.card-content-info-title {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  justify-content: space-between;
  flex-grow: 1;
}

.card-content-completeness {
  display: flex;
  gap: 20px;
  align-items: center;
}

.card-content-completeness-progress {
  flex-grow: 1;
}

.card-content-continue {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  justify-content: space-between;
  flex-grow: 1;
}
</style>