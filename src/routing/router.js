import * as VueRouter from "vue-router"
import Index from "@/pages/Index.vue"

const routes = [
    {path: '/', component: Index},
    {path: '/index', component: Index},
    {path: '/main', component: Index},
]

const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes,
})

export default router