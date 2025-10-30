import * as VueRouter from "vue-router"
import Index from "@/pages/Index.vue"
import APIDashboard from "@/pages/debug/APIDashboard.vue";
import CourseAbout from "@/pages/CourseAbout.vue";
import MyEducation from "@/pages/MyEducation.vue";
import CourseDetails from "@/pages/CourseDetails.vue";
import Profile from "@/pages/Profile.vue";
import Progress from "@/pages/Progress.vue";
import ModuleDetails from "@/pages/ModuleDetails.vue";

const routes = [
    {path: '/', component: Index},
    {path: '/index', component: Index},
    {path: '/main', component: Index},
    {path: '/api', component: APIDashboard},
    {path: '/course/about/:id', name: 'courseAbout', component: CourseAbout},
    {path: '/courses', name: 'myEducation', component: MyEducation},
    {path: '/course/:id', name: 'coursePage', component: CourseDetails},
    {path: '/profile', name: 'profile', component: Profile},
    {path: '/progress', name: 'progress', component: Progress},
    {path: '/course/:id/module/:mid', name: 'module', component: ModuleDetails}
]

const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes,
})

export default router