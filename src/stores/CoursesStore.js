import {defineStore} from 'pinia'
import {ref, computed} from "vue";
import {doRequest} from "@/helpers/NetworkManager.js";


export const useCoursesStore = defineStore('courses', () => {

    const updateRate = 60000;

    const state = ref({
        availableCourses: {
            storage: [],
            lastUpdate: Infinity
        },
        userCourses: {
            storage: {},
            lastUpdate: Infinity
        },
        courseDetails: {},
        courseModules: {},

    });

    const userCourses = computed(() => {
        if ((state.value.userCourses.lastUpdate - Date.now()) > updateRate) {
            loadUserCourses();
        }
        return state.value.userCourses.storage;
    });

    const availableCourses = computed(() => {
        if ((state.value.availableCourses.lastUpdate - Date.now()) > updateRate) {
            loadAvailableCourses();
        }
        return state.value.availableCourses.storage;
    });

    function loadAvailableCourses() {
        doRequest("coursesManager", "getAvailableCourses").then(
            (coursesResponse) => {
                if (coursesResponse.status === "success") {
                    for (let i = 0; i < coursesResponse.data.length; i++) {
                        coursesResponse.data[i].details = computed(() => {
                            state.value.courseDetails[coursesResponse.data[i].id] = state.value.courseDetails[coursesResponse.data[i].id] ?? {
                                data: null,
                                lastUpdate: Infinity
                            };
                            const details = state.value.courseDetails[coursesResponse.data[i].id];
                            if ((details.lastUpdate - Date.now()) > updateRate) {
                                loadCourseInfo(coursesResponse.data[i].id).then(
                                    infoResponse => {
                                        if (infoResponse.status === "success") {
                                            details.data = infoResponse.data;
                                            details.lastUpdate = Date.now();
                                        }
                                    }
                                );
                            }
                            return details.data;
                        });
                    }
                    state.value.availableCourses.storage = coursesResponse.data;
                    state.value.availableCourses.lastUpdate = Date.now();
                }
            }
        );
    }

    async function loadCourseInfo(courseId) {
        return doRequest("coursesManager", "getCourseInfo", {
            courseId
        });
    }

    function loadUserCourses() {
        doRequest("coursesManager", "getUserCourses").then(
            (userCoursesResponse) => {
                if (userCoursesResponse.status === "success") {
                    for (let i = 0; i < userCoursesResponse.data.length; i++) {
                        userCoursesResponse.data[i].modules = computed(() => {

                            state.value.courseModules[userCoursesResponse.data[i].id] = state.value.courseModules[userCoursesResponse.data[i].id] ?? {
                                data: null,
                                lastUpdate: Infinity
                            };
                            const modules = state.value.courseModules[userCoursesResponse.data[i].id];
                            if ((modules.lastUpdate - Date.now()) > updateRate) {
                                loadUserCourseModules(userCoursesResponse.data[i].id).then(
                                    modulesResponse => {
                                        if (modulesResponse.status === "success") {
                                            modules.data = modulesResponse.data;
                                            modules.lastUpdate = Date.now();
                                        }
                                    }
                                );
                            }
                            return modules.data;
                        });
                    }
                    state.value.userCourses.storage = userCoursesResponse.data;
                    state.value.userCourses.lastUpdate = Date.now();
                }
            }
        );
    }

    async function loadUserCourseModules(courseId) {
        return doRequest("coursesManager", "getUserCourseModules", {
            courseId
        });
    }

    return {availableCourses, userCourses};
});