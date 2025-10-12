<script setup>
import IconCircleLogo from "@/components/icons/logo/IconCircleLogo.vue";
import IconTriangleLogo from "@/components/icons/logo/IconTriangleLogo.vue";
import IconSquareLogo from "@/components/icons/logo/IconSquareLogo.vue";
import IconPentadecagonLogo from "@/components/icons/logo/IconPentadecagonLogo.vue";
import {useCoursesStore} from "@/stores/CoursesStore.js";


const courses = useCoursesStore();
const assetsUrl = new URL('@/assets', import.meta.url).href;

</script>

<template>
  <div>
    <section class="logo">
      <div class="logo-title text-white">
        <p>AnsarCodes —</p>
        <p>открытие перспектив</p>
        <p>через обучение и</p>
        <p>развитие в IT</p>
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

    <div class="title-for-block">Направления обучения</div>

    <section class="cards-container">
      <v-card v-for="course in courses.availableCourses" :key="course.id" elevation="4" class="card">
        <v-card-title class="card-title mt-3">
          <img :src="`${assetsUrl}/${course.icon || 'java-logo.png'}`"
               alt="language logotype"
               class="card-title-image">
          {{ course.title }}
        </v-card-title>
        <v-card-text class="card-text mt-12 d-flex flex-row">
          <div>
            {{ course.description }}
          </div>
          <div class="card-text-arrow text-summer-sky">
            ➔
          </div>
        </v-card-text>
        <div class="course-card-circle"></div>
      </v-card>
    </section>

    <div class="title-for-block">
      <p>Главный принцип наших курсов —</p>
      <p class="text-summer-sky">люди рядом</p>
    </div>

    <!--TODO: расположить верстку по макету -->
    <section class="content-about">
      <div class="content-about-left">
        <div class="back-holder group-of-people"></div>
        <div class="content-about-bold mt-5">Все наши преподаватели — практикующие разработчики</div>
        <div class="content-about-regular mt-3">Они обладают актуальными знаниями и опытом работы в индустрии. Это
          позволяет студентам получить навыки из первых рук и применять их на практике.
        </div>
      </div>
      <div class="content-about-right">
        <div class="content-about-right-top">
          <div class="content-about-right-top-left">
            <div class="back-holder checkbox-icon"></div>
            <div class="content-about-bold">В курсах нет лишней информации</div>
            <div class="content-about-regular mt-3">Обучение строится на основе практических заданий и проектов</div>
          </div>
          <div class="content-about-right-top-right">
            <div class="back-holder socials-icon"></div>
            <div class="content-about-bold">Вас сопровождает команда поддержки</div>
            <div class="content-about-regular mt-3">Готовая оказать помощь и ответить на ваши вопросы в любое время
            </div>
          </div>
        </div>

        <div class="content-about-right-bottom">
          <div class="back-holder calendar-icon"></div>
          <div class="content-about-bottom">
            <div class="content-about-bold">На курсах <span class="text-summer-sky">много практики</span></div>
            <div class="content-about-regular mt-3">Это позволяет студентам не только усвоить знания, но и
              непосредственно
              применить их в реальных проектах, углубляя свое понимание материала
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="feedback bg-alice-blue mb-5">
      <div class="feedback-title text-summer-sky mt-6">Остались вопросы?</div>
      <div class="feedback-text">Оставьте контакты — мы с вами свяжемся</div>
      <form class="mt-6">
        <input type="text" placeholder="Имя" class="input mb-6">
        <input type="text" placeholder="+7 (777) 777-77-77" class="input mb-6">
        <input type="text" placeholder="Электронная почта" class="input mb-6">
        <button type="submit" class="confirm bg-summer-sky text-white mb-6">Получить консультацию</button>
      </form>
      <div class="feedback-small mb-6">Нажимая кнопку, принимаю условия политики и пользовательского соглашения</div>
    </section>

  </div>
</template>

<style scoped>

.back-holder {
  background-position: center;
  background-repeat: no-repeat;
  background-size: 100%;
}

.back-holder.group-of-people {
  width: 314px;
  height: 158px;
  background-repeat: no-repeat;
  background-size: 100%;
  background-image: url("@/assets/card-icon-1.png");
}

.back-holder.checkbox-icon {
  position: relative;
  left: -20px;
  background-position: -5px center;
  width: 180px;
  height: 164px;
  background-repeat: no-repeat;
  background-size: 100%;
  background-image: url("@/assets/card-icon-2.png");
}

.back-holder.socials-icon {
  width: 180px;
  height: 164px;
  background-repeat: no-repeat;
  background-size: 100%;
  background-image: url("@/assets/card-icon-3.png");
}

.back-holder.calendar-icon {
  position: relative;
  left: -20px;
  flex-basis: 160px;
  flex-shrink: 0;
  height: 160px;
  background-repeat: no-repeat;
  background-size: 100%;
  background-image: url("@/assets/card-icon-4.png");
}

/* logo */
.logo {
  height: 428px;
  position: relative;
  z-index: -1;
  border-radius: 0 0 16px 16px;
  background: linear-gradient(180deg, rgba(63, 140, 255, 0.8) 0%, #3D39FF 100%);
}

.logo-title {
  font-size: 36px;
  font-weight: 700;
  line-height: 44px;
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
  transition: 0.3s ease;
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

/* title for block */
.title-for-block {
  font-size: 40px;
  font-weight: 600;
  line-height: 49px;
}

/* cards */
.card-title {
  font-size: 32px;
  font-weight: 700;
  line-height: 39px;
}

.card-title-image {
  width: 31px;
  height: 33px;
  vertical-align: bottom;
}

.card-text {
  font-size: 16px;
  font-weight: 500;
  line-height: 20px;
}

.card-text-arrow {
  font-style: normal;
  font-weight: 500;
  font-size: 34px;
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

/* about */
.content-about {
  display: flex;
  flex-flow: wrap;
  column-gap: 20px;
}

.about-image {
  margin-bottom: -20px;
  margin-left: -40px;
}

.about-image-left {
  margin-top: -40px;
  margin-left: -40px;
}

.content-about-bold {
  font-size: 20px;
  font-weight: 600;
  line-height: 24px;
}

.content-about-regular {
  font-size: 20px;
  font-weight: 500;
  line-height: 24px;
}

.content-about-left {
  flex-basis: 30%;
  flex-grow: 1;
  min-width: 344px;
  background: linear-gradient(180deg, #FFFFFF 0%, #EEF3F3 100%);
  border-radius: 16px;
  padding: 10px 20px 10px 20px;
}

.content-about-right {
  flex-basis: calc(70% - 21px);
  flex-grow: 1;
  min-width: 344px;
  display: flex;
  flex-flow: wrap;
  gap: 20px;
}

.content-about-right-top {
  display: flex;
  flex-flow: wrap;
  gap: 20px;
}

.content-about-right-top-left {
  flex-basis: 45%;
  flex-grow: 1;
  min-width: 344px;
  background: linear-gradient(180deg, #FFFFFF 0%, #EEF3F3 100%);
  border-radius: 16px;
  padding: 10px 20px 10px 20px;
}

.content-about-right-top-right {
  flex-basis: 45%;
  flex-grow: 1;
  min-width: 344px;
  background: linear-gradient(180deg, #FFFFFF 0%, #EEF3F3 100%);
  border-radius: 16px;
  padding: 10px 20px 10px 20px;
}

.content-about-right-bottom {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  flex-basis: 100%;
  flex-grow: 1;
  min-width: 344px;
  background: linear-gradient(180deg, #FFFFFF 0%, #EEF3F3 100%);
  border-radius: 16px;
  padding: 10px 20px 10px 20px;
}

.content-about-bottom {
  flex-basis: 600px;
}

/* feedback */
.feedback {
  border-radius: 16px;
  padding: 10px 20px 10px 20px;
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

@media only screen and (max-width: 758px) {
  .logo-image {
    transform: scale(0.5);
    right: -30%;
    bottom: -50%;
  }
}
</style>