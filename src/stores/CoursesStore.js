import {defineStore} from 'pinia'
import {computed, ref, watch} from "vue";
import {doRequest} from "@/helpers/NetworkManager.js";


export const useCoursesStore = defineStore('courses', () => {

    // let eagerMode = ref(false);
    let updateRate = 60000;
    const state = ref({
        availableCourses: {
            storage: [],
            lastUpdate: Infinity
        },
        userCourses: {
            storage: {},
            lastUpdate: Infinity
        }
    });
    const promises = [];

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

    function getComputableNode(populateWithFunc, ...populateWithArgs) {
        const realStorage = ref({
            data: [],
            lastUpdate: Infinity
        });
        const fullFiller = () => {
            if ((realStorage.value.lastUpdate - Date.now()) > updateRate) {
                promises.push(populateWithFunc.apply(this, populateWithArgs.concat(realStorage)).then(
                    infoResponse => {
                        if (infoResponse.status === "success") {
                            realStorage.value.data = infoResponse.data; //implement soft merging, not replacing
                            realStorage.value.lastUpdate = Date.now();
                        } else {
                            console.log(`${populateWithFunc.name} called with ${populateWithArgs.join("|")} got error: <${infoResponse.data}>`);
                        }
                    }
                ));
            }
            return realStorage.value.data;
        }

        return computed(fullFiller);
    }

    function loadAvailableCourses() {
        promises.push(doRequest("coursesManager", "getAvailableCourses").then(
            (coursesResponse) => {
                if (coursesResponse.status === "success") {
                    for (let i = 0; i < coursesResponse.data.length; i++) {
                        coursesResponse.data[i].details = getComputableNode(
                            loadCourseInfo,
                            coursesResponse.data[i].id
                        );
                    }
                    state.value.availableCourses.storage = coursesResponse.data;
                    state.value.availableCourses.lastUpdate = Date.now();
                }
            }
        ));
    }

    async function loadCourseInfo(courseId) {
        return doRequest("coursesManager", "getCourseInfo", {
            courseId
        });
    }

    function loadUserCourses() {
        promises.push(doRequest("coursesManager", "getUserCourses").then(
            (userCoursesResponse) => {
                if (userCoursesResponse.status === "success") {
                    for (let i = 0; i < userCoursesResponse.data.length; i++) {
                        userCoursesResponse.data[i].modules = getComputableNode(
                            loadUserCourseModules,
                            userCoursesResponse.data[i].id
                        );
                    }
                    state.value.userCourses.storage = userCoursesResponse.data;
                    state.value.userCourses.lastUpdate = Date.now();
                }
            }
        ));
    }

    async function loadUserCourseModules(courseId) {
        const modules = await doRequest("coursesManager", "getUserCourseModules", {
            courseId
        });
        if (modules.status === "success") {
            for (let i = 0; i < modules.data.length; i++) {
                modules.data[i].resources = modules.data[i]?.resources ?? [];

                modules.data[i].resources.articles = getComputableNode(
                    loadUserCourseModulesArticlesTree,
                    courseId, modules.data[i].id
                );
                modules.data[i].resources.homework = getComputableNode(
                    loadUserCourseModuleHomework,
                    courseId, modules.data[i].id
                );
            }
        }
        return modules;
    }

    async function loadUserCourseModulesArticlesTree(courseId, moduleId) {
        const articlesTree = await doRequest("coursesManager", "getUserCourseModuleArticlesTree", {
            courseId, moduleId
        });

        if (articlesTree.status === "success") {
            const addComputableFields = function (treeNode, path = []) {
                if (Array.isArray(treeNode)) {
                    treeNode.forEach((item) => {
                        addComputableFields(item);
                    })
                } else {

                    switch (treeNode.type) {
                        case "group": {
                            treeNode.content = treeNode.content ?? [];
                            for (let i = 0; i < treeNode.content.length; i++) {
                                addComputableFields(treeNode.content[i], path.concat(treeNode.id));
                            }
                            break;
                        }

                        case "article": {

                            treeNode.content = getComputableNode(
                                loadUserCourseModuleArticle,
                                courseId, moduleId, path.concat(treeNode.id).join(",")
                            );
                            break;
                        }
                    }

                    treeNode.completed = ref(treeNode.completed);

                    watch(treeNode.completed, (status) => {
                        doRequest("coursesManager", "markMaterialAsCompleted", {
                            courseId, moduleId, articlePath: (path.concat(treeNode.id).join(",")), status
                        }).then(
                            () => {
                                treeNode.completed.value = status;
                                loadUserCourses();
                            }
                        );
                    });
                }
            }

            addComputableFields(articlesTree.data);
        }
        return articlesTree;
    }

    async function loadUserCourseModuleArticle(courseId, moduleId, articlePath) {
        return await doRequest("coursesManager", "getUserCourseModuleArticle", {
            courseId, moduleId, articlePath
        });
    }

    async function loadUserCourseModuleHomework(courseId, moduleId, storage) {
        const response = await doRequest("coursesManager", "getUserCourseModuleHomework", {
            courseId, moduleId
        });

        if (response.status === "success") {
            response.data.tools = {
                addComment(message) {
                    doRequest("coursesManager", "addHomeworkComment", {
                        courseId, moduleId, message
                    }).then(res => {
                        if (res.status === "success") {
                            storage.value.data.comments = storage.value.data.comments ?? [];
                            storage.value.data.comments.push(res.data);
                        }
                    });
                },

                uploadHomework(file) {
                    doRequest("coursesManager", "addHomeworkSubmission", {
                        courseId, moduleId, file
                    }).then(res => {
                        if (res.status === "success") {
                            storage.value.data.submissions = storage.value.data.submissions ?? [];
                            storage.value.data.submissions.push(res.data);
                        }
                    });
                },

                downloadHomework(fileHash) {
                    doRequest("coursesManager", "downloadHomeworkFile", {
                        courseId, moduleId, fileHash
                    }).then(console.log);
                },
            };
        }

        return response;
    }

    return {availableCourses, userCourses};
});
