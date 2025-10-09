<script setup>
import IconInstagram from "@/components/icons/IconInstagram.vue"
import IconTelegram from "@/components/icons/IconTelegram.vue"
</script>

<template>
  <footer>
    <div class="footer-title pb-0">
      AnsarCodes
    </div>

    <div class="sections">

      <section>
        <div class="footer-title-small">
          Courses
        </div>
        <div class="footer-contact mt-0">
          <div>
            <span>Java</span>
            <span class="pl-3">Python</span>
            <span class="pl-3">IOS</span>
            <span class="pl-3">Frontend</span>
          </div>
          <div>
            <span>C#</span>
            <span class="pl-3">Android</span>
            <span class="pl-3">JavaScript</span>
            <span class="pl-3">Basic</span>
          </div>
        </div>
      </section>

      <section>
        <div class="footer-title-small">
          Contacts
        </div>
        <div class="footer-contact mt-0">
          <div>+7 (777) 777-77-77</div>
          <div>ansarshayekin1@gmail.com</div>
        </div>
      </section>

      <section>
        <div class="footer-title-small">
        </div>
        <div class="footer-contact mt-0">
          <div>Social medias
            <IconInstagram/>
            <IconTelegram/>
          </div>
          <div>Feedback form</div>
        </div>
      </section>

    </div>
  </footer>

</template>

<style scoped>

footer {
  flex-direction: column;
  /*TODO: background #F6F8F9 по всей ширине*/
  background: #FFFFFF;
  display: flex;
  justify-content: space-between;
  padding: 10px 40px;
  align-content: center;
}

footer .sections {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  row-gap: 20px;
  padding: 20px 0;
}

.footer-title {
  font-family: Montserrat, sans-serif;
  font-size: 32px;
  font-weight: 700;
  line-height: 39px;
  letter-spacing: 0;
  text-align: left;
  color: #83919F;
}

.footer-title-small {
  min-height: 20px;
  font-family: Montserrat, sans-serif;
  font-size: 16px;
  font-weight: 500;
  line-height: 20px;
  letter-spacing: 0;
  text-align: left;
  color: #BDBDBD;
  margin-bottom: 10px;
}

.footer-contact {
  font-family: Montserrat, sans-serif;
  font-size: 20px;
  font-weight: 500;
  line-height: 24px;
  letter-spacing: 0;
  text-align: left;
  color: #83919F;
}
</style>