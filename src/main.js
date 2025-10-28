import './assets/styles/main.css'
import 'vuetify/styles'

import {createApp} from 'vue'
import router from "@/routing/router.js"
import vuetify from "@/vuetify/vuetify.js"

import App from '@/App.vue'
import {createPinia} from "pinia";

const pinia = createPinia();

createApp(App)
    .use(router)
    .use(vuetify)
    .use(pinia)
    .mount('#app')