import './assets/main.css'
import 'vuetify/styles'

import {createApp} from 'vue'
import router from "@/routing/router.js"
import vuetify from "@/vuetify/vuetify.js"
import App from '@/App.vue'

createApp(App)
    .use(router)
    .use(vuetify)
    .mount('#app')