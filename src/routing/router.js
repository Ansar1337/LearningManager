import * as VueRouter from "vue-router"
import Index from "@/pages/Index.vue"
import APIDashboard from "@/pages/debug/APIDashboard.vue";
import CourseAbout from "@/pages/CourseAbout.vue";
import MyEducation from "@/pages/MyEducation.vue";

const routes = [
    {path: '/', component: Index},
    {path: '/index', component: Index},
    {path: '/main', component: Index},
    {path: '/api', component: APIDashboard},
    {path: '/course/about/:id', name: 'courseAbout', component: CourseAbout},
    {path: '/courses', name: 'myEducation', component: MyEducation}
]

const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes,
})

export default router