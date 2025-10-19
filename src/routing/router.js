import * as VueRouter from "vue-router"
import Index from "@/pages/Index.vue"
import APIDashboard from "@/pages/debug/APIDashboard.vue";
import CourseAbout from "@/pages/CourseAbout.vue";

const routes = [
    {path: '/', component: Index},
    {path: '/index', component: Index},
    {path: '/main', component: Index},
    {path: '/api', component: APIDashboard},
    {path: '/course/about/:id', name: "courseAbout", component: CourseAbout}
]

const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes,
})

export default router