<script setup>
import IconInstagram from "@/components/icons/IconInstagram.vue"
import IconTelegram from "@/components/icons/IconTelegram.vue"
</script>

<template>
  <footer class="bg-alice-blue">
    <div></div>

    <div class="footer-container">
      <div class="footer-title text-light-slay-grey pb-0">
        AnsarCodes
      </div>

      <div class="sections">
        <section>
          <div class="footer-title-small text-silver">
            Направления
          </div>
          <div class="footer-contact text-light-slay-grey mt-0">
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
          <div class="footer-title-small text-silver">
            Контакты
          </div>
          <div class="footer-contact text-light-slay-grey mt-0">
            <div>+7 (777) 777-77-77</div>
            <div>ansarcodes@gmail.com</div>
          </div>
        </section>

        <section>
          <div class="footer-title-small text-silver">
          </div>
          <div class="footer-contact text-light-slay-grey mt-0">
            <div>Социальные сети
              <IconInstagram/>
              <IconTelegram/>
            </div>
            <div>Форма обратной связи</div>
          </div>
        </section>
      </div>
    </div>

    <div></div>
  </footer>
</template>

<style scoped>

.footer-container {
  margin: 0 auto;
  padding: 10px 0;
  display: flex;
  flex: 1 1 auto;
  gap: 10px;
  flex-direction: column;
  justify-content: space-between;
  align-content: center;
  max-width: min(1920px, 85%);
}

footer .sections {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  row-gap: 20px;
  column-gap: 10px;
  padding: 20px 0;
}

.footer-title {
  font-size: 32px;
  font-weight: 700;
  line-height: 40px;
}

.footer-title-small {
  min-height: 20px;
  font-size: 16px;
  /*font-weight: 500;*/
  line-height: 20px;
  color: #BDBDBD;
  margin-bottom: 10px;
}

.footer-contact {
  font-size: 20px;
  /*font-weight: 500;*/
  line-height: 24px;
}
</style>