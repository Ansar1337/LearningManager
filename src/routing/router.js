import * as VueRouter from "vue-router"
import Index from "@/pages/Index.vue"
import APIDashboard from "@/pages/debug/APIDashboard.vue";
import CourseAbout from "@/pages/CourseAbout.vue";
import MyEducation from "@/pages/MyEducation.vue";
import CourseDetails from "@/pages/CourseDetails.vue";
import Profile from "@/pages/Profile.vue";
import Progress from "@/pages/Progress.vue";
import ModuleDetails from "@/pages/ModuleDetails.vue";
import Articles from "@/pages/Articles.vue";
import Homework from "@/pages/Homework.vue";
import Test from "@/pages/Test.vue";

const routes = [
    {path: '/', component: Index},
    {path: '/index', component: Index},
    {path: '/main', component: Index},
    {path: '/api', component: APIDashboard},
    {path: '/about/:id/', name: 'courseAbout', component: CourseAbout},
    {path: '/myCourses', name: 'myEducation', component: MyEducation},
    {path: '/myCourses/course/:id', name: 'coursePage', component: CourseDetails},
    {path: '/profile', name: 'profile', component: Profile},
    {path: '/progress', name: 'progress', component: Progress},
    {path: '/myCourses/course/:id/module/:mid', name: 'module', component: ModuleDetails},
    {path: '/myCourses/course/:id/module/:mid/articles', name: 'articles', component: Articles},
    {path: '/myCourses/course/:id/module/:mid/homework', name: 'homework', component: Homework},
    {path: '/myCourses/course/:id/module/:mid/test', name: 'test', component: Test},
]

const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes,
})

export default router