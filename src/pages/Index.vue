<script setup>
const studies = [
  {title: "IOS", description: "Development for Apple devices"},
  {title: "Java", description: "Development for Apple devices"},
  {title: "Android", description: "Development for Apple devices"},
  {title: "Python", description: "Development for Apple devices"},
  {title: "JavaScript", description: "Development for Apple devices"},
  {title: "Frontend", description: "Development for Apple devices"},
  {title: "C#", description: "Development for Apple devices"},
  {title: "Basic", description: "Development for Apple devices"},
]
</script>

<template>
  <v-row>
    <v-col cols="8" offset="2">
      <div class="logo">
        <v-row>
          <v-col cols="6">
            <div class="logo-title">
              <div>AnsarCodes —</div>
              <div>opening up prospects</div>
              <div>through education and</div>
              <div>training in IT</div>
            </div>
          </v-col>
          <v-col cols="5" offset="1">
            <div class="logo-phone">
              <div class="logo-phone-front"></div>
              <div class="logo-phone-back"></div>
            </div>
          </v-col>
        </v-row>
      </div>
    </v-col>
  </v-row>

  <v-row>
    <v-col cols="8" offset="2">
      <div class="content-study">Areas of study</div>
    </v-col>
    <v-col cols="8" offset="2">
      <v-sheet class="d-flex flex-wrap flex-back ">
        <v-sheet v-for="study in studies" class="ma-2 pa-2 flex-back ">
          <v-card height="200px" width="200px" elevation="4">
            <v-card-title>{{ study.title }}</v-card-title>
            <v-card-text>{{ study.description }}</v-card-text>
          </v-card>
        </v-sheet>
      </v-sheet>
    </v-col>
  </v-row>

  <v-row>
    <v-col cols="8" offset="2">
      Feedback
    </v-col>
  </v-row>
</template>

<style scoped>
.logo {
  height: 428px;
  position: relative;
  top: -45px;
  z-index: -1;
  border-radius: 16px;
  background: linear-gradient(180deg, #6B31E4 0%, rgba(42, 120, 236, 0.8) 100%);
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

.logo-phone {
  z-index: -1;
}

.logo-phone-front {
  width: 208px;
  height: 360px;
  position: absolute;
  top: 133px;
  transform: rotate(11deg);
  background: #56CCF2;
}

.logo-phone-back {
  width: 208px;
  height: 361px;
  position: absolute;
  top: 133px;
  margin-left: 30px;
  margin-top: 5px;
  z-index: -2;
  transform: rotate(11deg);
  background: #2D9CDB;
}

.content-study {
  font-family: Montserrat, sans-serif;
  font-size: 40px;
  font-weight: 600;
  line-height: 49px;
  letter-spacing: 0;
  text-align: left;
}

.flex-back {
  background: #F5F5F5;
}
</style>