<script setup>

</script>

<template>
  <v-row class="header">
    <v-col cols="2" offset="2" class="header-title">
      AnsarCodes
    </v-col>
    <v-col cols="2" offset="4">
      <v-btn color="#2D9CDB" class="float-right header-btn">
        Login
      </v-btn>
    </v-col>
  </v-row>
</template>

<style scoped>
.header {
  border-bottom: 1px solid #E0E0E0;
  background: #F5F5F5;
}

.header-title {
  font-family: Montserrat, sans-serif;
  font-size: 24px;
  font-weight: 800;
  line-height: 29px;
  letter-spacing: 0;
  text-align: left;
  color: #2D9CDB;
}

.header-btn {
  color: #FFFFFF;
}
</style>