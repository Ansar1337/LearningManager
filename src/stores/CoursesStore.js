import {defineStore} from 'pinia'
import {ref, watch} from "vue";
import {doRequest} from "@/helpers/NetworkManager.js";
import {getComputableNode} from "@/helpers/SmartCompute.js";

export const useCoursesStore = defineStore('courses', () => {

    // let eagerMode = ref(false);
    let updateRate = 60000;

    async function loadAvailableCourses() {
        const coursesResponse = await doRequest("coursesManager", "getAvailableCourses");
        if (coursesResponse.status === "success") {
            for (let i = 0; i < coursesResponse.data.length; i++) {
                coursesResponse.data[i].details = getComputableNode(
                    updateRate,
                    loadCourseInfo,
                    coursesResponse.data[i].id
                );
            }
        }
        return coursesResponse;
    }

    async function loadCourseInfo(courseId) {
        return doRequest("coursesManager", "getCourseInfo", {
            courseId
        });
    }

    async function loadUserCourses() {
        const userCoursesResponse = await doRequest("coursesManager", "getUserCourses");
        if (userCoursesResponse.status === "success") {
            for (let i = 0; i < userCoursesResponse.data.length; i++) {
                userCoursesResponse.data[i].modules = getComputableNode(
                    updateRate,
                    loadUserCourseModules,
                    userCoursesResponse.data[i].id
                );
            }
        }
        return userCoursesResponse;
    }

    async function loadUserCourseModules(courseId) {
        const modules = await doRequest("coursesManager", "getUserCourseModules", {
            courseId
        });
        if (modules.status === "success") {
            for (let i = 0; i < modules.data.length; i++) {
                modules.data[i].resources = modules.data[i].resources ?? [];

                modules.data[i].resources.articles = getComputableNode(
                    updateRate,
                    loadUserCourseModulesArticlesTree,
                    courseId, modules.data[i].id
                );
                modules.data[i].resources.homework = getComputableNode(
                    updateRate,
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
                                updateRate,
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

    const availableCourses = getComputableNode(
        updateRate,
        loadAvailableCourses
    );

    const userCourses = getComputableNode(
        updateRate,
        loadUserCourses
    );

    return {
        availableCourses,
        userCourses
    };
});
