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
            (coursesResponse) => {
                if (coursesResponse.status === "success") {
                    for (let i = 0; i < coursesResponse.data.length; i++) {
                        coursesResponse.data[i].details = computed(() => {
                            state.value.courseDetails[coursesResponse.data[i].id] = state.value.courseDetails[coursesResponse.data[i].id] ?? {
                                data: null,
                                lastUpdate: Infinity
                            };
                            const details = state.value.courseDetails[coursesResponse.data[i].id];
                            if ((!details.data) || (details.lastUpdate - Date.now()) > 60000) {
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

    return {availableCourses, userCourses};
});