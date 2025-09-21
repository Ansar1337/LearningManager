<script setup>
import IconInstagram from "@/components/icons/IconInstagram.vue"
import IconTelegram from "@/components/icons/IconTelegram.vue"
</script>

<template>
  <v-row>
    <v-col cols="8" offset="2">
      <v-row>
        <v-col cols="12" class="footer-title pb-0">
          AnsarCodes
        </v-col>
      </v-row>

      <v-row class="footer-title-small">
        <v-col cols="4">
          Navigation
        </v-col>
        <v-col cols="4">
          Contacts
        </v-col>
      </v-row>

      <v-row class="footer-contact mt-0">
        <v-col cols="4">
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
        </v-col>
        <v-col cols="4">
          <div>+7 (777) 777-77-77</div>
          <div>ansarcodes@gmail.com</div>
        </v-col>
        <v-col cols="4">
          <div>Social medias
            <IconInstagram/>
            <IconTelegram/>
          </div>
          <div>Feedback form</div>
        </v-col>
      </v-row>
    </v-col>
  </v-row>
</template>

<style scoped>
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
  font-family: Montserrat, sans-serif;
  font-size: 16px;
  font-weight: 500;
  line-height: 20px;
  letter-spacing: 0;
  text-align: left;
  color: #BDBDBD;
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