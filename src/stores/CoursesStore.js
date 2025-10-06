import {defineStore} from 'pinia'
import {ref, computed} from "vue";
import {doRequest} from "@/helpers/NetworkManager.js";


export const UseCoursesStore = defineStore('courses', () => {

    const state = ref({
        availableCourses: {
            storage: [],
            lastUpdate: Infinity
        },
        courseDetails: {},
        userCourses: [],
    });

    const userCourses = computed(() => {
        return [];
    });

    const availableCourses = computed(() => {
        if ((state.value.availableCourses.lastUpdate - Date.now()) > 60000) {
            loadAvailableCourses();
        }
        return state.value.availableCourses.storage;
    });

    function loadAvailableCourses() {
        doRequest("coursesManager", "getAvailableCourses").then(
            (res) => {
                if (res.status === "success") {
                    for (let i = 0; i < res.data.length; i++) {
                        res.data[i].details = computed(() => {
                            state.value.courseDetails[res.data.id] = state.value.courseDetails[res.data.id] ?? {
                                data: null,
                                lastUpdate: Infinity
                            };
                            const details = state.value.courseDetails[res.data.id];
                            if ((!details.data) || (details.lastUpdate - Date.now()) > 60000) {
                                loadCourseInfo(res.data.id).then(
                                    res => {
                                        if (res.status === "success") {
                                            details.data = res.data;
                                            details.lastUpdate = Date.now();
                                        }
                                    }
                                );
                            }
                            return details.data;
                        });
                    }
                    state.value.availableCourses.storage = res.data;
                    state.value.availableCourses.lastUpdate = Date.now();
                }
            }
        );
    }

    async function loadCourseInfo(courseId) {
        //заглушка
        return {
            status: "success",
            data: {
                date: 1,
                timing: 2,
                modules: 3,
                longDescription: 4
            }
        };
    }

    return {availableCourses, userCourses};
});