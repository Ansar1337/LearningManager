<script setup>
import IconCircleLogo from "@/components/icons/logo/IconCircleLogo.vue";
import IconTriangleLogo from "@/components/icons/logo/IconTriangleLogo.vue";
import IconSquareLogo from "@/components/icons/logo/IconSquareLogo.vue";
import IconPentadecagonLogo from "@/components/icons/logo/IconPentadecagonLogo.vue";
import {UseCoursesStore} from "@/stores/CoursesStore.js";

const courses = UseCoursesStore();
const assetsUrl = new URL('@/assets', import.meta.url).href;
</script>

<template>
  <div>
    <section class="logo">
      <div class="logo-title">
        <p>AnsarCodes —</p>
        <p>opening up prospects</p>
        <p>through education and</p>
        <p>training in IT</p>
      </div>
      <section class="logo-image">
        <img src="@/assets/laptop-logo.png" alt="logo image">
        <div class="large-circle"></div>
        <div class="small-circle"></div>
        <IconCircleLogo class="icon-circle"/>
        <IconTriangleLogo class="icon-triangle"/>
        <IconSquareLogo class="icon-square"/>
        <IconPentadecagonLogo class="icon-pentadecagon"/>
      </section>
    </section>

    <h2 class="content-study">Areas of study</h2>

    <section class="cards-container">
      <v-card v-for="course in courses.availableCourses" :key="course.id" elevation="4" class="card">
        <v-card-title class="card-title mt-3">
          <img :src="`${assetsUrl}/${course.icon || 'java-logo.png'}`"
               class="card-title-image">
          {{ course.title }}
        </v-card-title>
        <v-card-text class="card-text mt-12 d-flex flex-row">
          <div>
            {{ course.description }}
          </div>
          <div class="card-text-arrow">
            ➔
          </div>
        </v-card-text>
        <div class="course-card-circle"></div>
      </v-card>
    </section>

    <h2 class="content-study">
      <div>Главный принцип наших курсов —</div>
      <div style="color: #2D9CDB">люди рядом</div>
    </h2>

    <!--TODO: расположить верстку по макету -->
    <section class="content-about">
      <div class="content-about-1">
        <img src="@/assets/card-icon-1.png" width="314">
        <div class="content-about-bold mt-5">Все наши преподаватели — практикующие разработчики</div>
        <div class="content-about-regular mt-5">Они обладают актуальными знаниями и опытом работы в индустрии. Это
          позволяет студентам получить навыки из первых рук и применять их на практике.
        </div>
      </div>
      <div class="content-about-2">
        <img src="@/assets/card-icon-2.png" width="180">
        <div class="content-about-bold">В курсах нет лишней информации</div>
        <div class="content-about-regular mt-5">Обучение строится на основе практических заданий и проектов</div>
      </div>
      <div class="content-about-3">
        <img src="@/assets/card-icon-3.png" width="180">
        <div class="content-about-bold">Вас сопровождает команда поддержки</div>
        <div class="content-about-regular mt-5">Готовая оказать помощь и ответить на ваши вопросы в любое время</div>
      </div>
      <div class="content-about-4 float-end">
        <img src="@/assets/card-icon-4.png" width="180">
        <div class="content-about-bold">На курсах <span style="color: #2D9CDB">много практики</span></div>
        <div class="content-about-regular mt-5">Это позволяет студентам не только усвоить знания, но и непосредственно
          применить их в реальных проектах, углубляя свое понимание материала
        </div>
      </div>
    </section>

    <section class="feedback mb-5">
      <div class="feedback-title mt-6">Остались вопросы?</div>
      <div class="feedback-text">Оставьте контакты — мы с вами свяжемся</div>
      <form class="mt-6 mb-6">
        <input type="text" placeholder="Имя" class="input">
        <input type="text" placeholder="+7 (777) 777-77-77" class="input">
        <input type="text" placeholder="Электронная почта" class="input">
        <button type="submit" class="confirm">Получить консультацию</button>
      </form>
      <div class="feedback-small mb-6">Нажимая кнопку, принимаю условия политики и пользовательского соглашения</div>
    </section>

  </div>
</template>

<style scoped>

.feedback {
  background: #F6F8F9;
  border-radius: 16px;
  padding: 10px 20px 10px 20px;
}

.feedback-title {
  font-family: Montserrat, sans-serif;
  font-size: 40px;
  font-weight: 600;
  line-height: 49px;
  letter-spacing: 0em;
  text-align: left;
  color: #2D9CDB;
}

.feedback-small {
  font-family: Montserrat, sans-serif;
  font-size: 12px;
  font-weight: 400;
  line-height: 15px;
  letter-spacing: 0em;
  text-align: left;
}

.feedback-text {
  font-family: Montserrat, sans-serif;
  font-size: 18px;
  font-weight: 400;
  line-height: 22px;
  letter-spacing: 0em;
  text-align: left;
}

.input {
  border: 1px solid gray;
  border-radius: 5px;
  margin-right: 10px;
  padding-left: 7px;
}

.confirm {
  background: #2D9CDB;
  border-radius: 5px;
  padding: 2px 20px 2px 20px;
  color: #FFFFFF;
  font-family: Montserrat, sans-serif;
  font-size: 16px;
  line-height: 20px;
  letter-spacing: 0em;
  text-align: left;
}

.content-about {
  display: flex;
  flex-flow: wrap;
  gap: 20px;
}

.content-about-bold {
  font-family: Montserrat, sans-serif;
  font-size: 20px;
  font-weight: 600;
  line-height: 24px;
}

.content-about-regular {
  font-family: Montserrat, sans-serif;
  font-size: 20px;
  font-weight: 500;
  line-height: 24px;
}

.content-about-1 {
  min-width: 344px;
  max-width: 40%;
  background: linear-gradient(180deg, #FFFFFF 0%, #EEF3F3 100%);
  border-radius: 16px;
  padding: 10px 20px 10px 20px;
}

.content-about-2 {
  min-width: 250px;
  max-width: 28%;
  background: linear-gradient(180deg, #FFFFFF 0%, #EEF3F3 100%);
  border-radius: 16px;
  padding: 10px 20px 10px 20px;
}

.content-about-3 {
  min-width: 250px;
  max-width: 28%;
  background: linear-gradient(180deg, #FFFFFF 0%, #EEF3F3 100%);
  border-radius: 16px;
  padding: 10px 20px 10px 20px;
}

.content-about-4 {
  min-width: 250px;
  max-width: 60%;
  background: linear-gradient(180deg, #FFFFFF 0%, #EEF3F3 100%);
  border-radius: 16px;
  padding: 10px 20px 10px 20px;
}

.card-title {
  font-family: Montserrat, sans-serif;
  font-size: 32px;
  font-weight: 700;
  line-height: 39px;
  letter-spacing: 0em;
  text-align: left;
}

.card-title-image {
  width: 31px;
  height: 33px;
  vertical-align: bottom;
}

.card-text {
  font-family: Montserrat, sans-serif;
  font-size: 16px;
  font-weight: 500;
  line-height: 20px;
  letter-spacing: 0em;
  text-align: left;
}

.card-text-arrow {
  font-family: Montserrat, sans-serif;
  font-style: normal;
  font-weight: 500;
  font-size: 34px;
  color: #2D9CDB;
  margin: auto;
}

.course-card-circle {
  width: 418px;
  height: 282px;
  position: absolute;
  top: 20px;
  z-index: -1;
  left: 150px;
  opacity: 10%;
  border-radius: 50%;
  background: linear-gradient(180deg, #7AAFFF 0%, rgba(208, 227, 255, 0) 100%);
}

.cards-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.cards-container .card {
  background: linear-gradient(180deg, #FFFFFF 0%, rgba(243, 251, 255, 0.8) 100%);
  min-height: 150px;
}

.logo {
  height: 428px;
  position: relative;
  z-index: -1;
  border-radius: 0 0 16px 16px;
  background: linear-gradient(180deg, rgba(63, 140, 255, 0.8) 0%, #3D39FF 100%);
}

.logo-title {
  font-family: Montserrat, sans-serif;
  font-size: 36px;
  font-weight: 700;
  line-height: 44px;
  letter-spacing: 0;
  text-align: left;
  color: #FFFFFF;
  position: absolute;
  top: 114px;
  margin-left: 60px;
  z-index: 1;
}

.logo-image img {
  width: 537px;
  height: 537px;
}

.logo-image {
  z-index: -1;
  position: absolute;
  right: min(100px, calc(1% + 30px));
}

.large-circle {
  width: 537px;
  height: 537px;
  background: linear-gradient(180deg, rgba(67, 187, 255, 0.49) 0%, rgba(0, 135, 211, 0) 100%);
  position: absolute;
  top: -20px;
  border-radius: 50%;
  z-index: -3;
  clip: rect(0px 537px 448px 0px);
  opacity: 20%;
}

.small-circle {
  width: 432px;
  height: 432px;
  background: linear-gradient(180deg, rgba(67, 187, 255, 0.49) 0%, rgba(0, 135, 211, 0) 100%);
  border-radius: 50%;
  position: absolute;
  top: 32px;
  left: 52px;
  z-index: -2;
  opacity: 80%;
}

.icon-circle {
  position: absolute;
  top: -40px;
  left: -80px;
}

.icon-triangle {
  position: absolute;
  top: 220px;
  left: calc(-10vw + 30px);
}

.icon-square {
  position: absolute;
  top: 120px;
  right: -50px;
}

.icon-pentadecagon {
  position: absolute;
  top: 280px;
  right: -95px;
}

.content-study {
  font-family: Montserrat, sans-serif;
  font-size: 40px;
  font-weight: 600;
  line-height: 49px;
  letter-spacing: 0;
  text-align: left;
}

@media only screen and (max-width: 758px) {
  .logo-image {
    visibility: hidden;
  }
}
</style>