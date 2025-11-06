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
import {useUserStore} from "@/stores/UserStore.js";
import NotFound from "@/pages/NotFound.vue";

const routes = [
    {path: '/', name: 'main', component: Index},
    {path: '/api', component: APIDashboard},
    {path: '/about/:id/', name: 'courseAbout', component: CourseAbout},
    {path: '/myCourses', name: 'myEducation', component: MyEducation, meta: {requiresAuth: true}},
    {path: '/myCourses/course/:id', name: 'coursePage', component: CourseDetails, meta: {requiresAuth: true}},
    {path: '/profile', name: 'profile', component: Profile, meta: {requiresAuth: true}},
    {path: '/progress', name: 'progress', component: Progress, meta: {requiresAuth: true}},
    {path: '/myCourses/course/:id/module/:mid', name: 'module', component: ModuleDetails, meta: {requiresAuth: true}},
    {
        path: '/myCourses/course/:id/module/:mid/articles',
        name: 'articles',
        component: Articles,
        meta: {requiresAuth: true}
    },
    {
        path: '/myCourses/course/:id/module/:mid/homework',
        name: 'homework',
        component: Homework,
        meta: {requiresAuth: true}
    },
    {path: '/myCourses/course/:id/module/:mid/test', name: 'test', component: Test, meta: {requiresAuth: true}},
    {path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound},
]

const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes,
})

router.beforeEach(async (to, from, next) => {
    const userStore = useUserStore();
    const isAuthenticated = await userStore.session.loggedIn;
    if (to.matched.some(record => record.meta.requiresAuth) && !isAuthenticated) {
        next('/');
    } else {
        next();
    }
});


export default router;