import {defineStore} from 'pinia'
import {reactive, ref, watch} from "vue";
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

                modules.data[i].resources.test = getComputableNode(
                    updateRate,
                    loadUserCourseModuleTest,
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
                            treeNode.content = reactive(treeNode.content ?? []);
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

                    watch(() => treeNode.completed, (status) => {
                        doRequest("coursesManager", "markMaterialAsCompleted", {
                            courseId, moduleId, articlePath: (path.concat(treeNode.id).join(",")), status
                        }).then(
                            () => {
                                treeNode.completed = status;
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

    async function markCommentAsRead(courseId, moduleId, commentId) {
        return await doRequest("coursesManager", "markCommentAsRead", {courseId, moduleId, commentId});
    }

    async function loadUserCourseModuleHomework(courseId, moduleId, storage) {
        const response = await doRequest("coursesManager", "getUserCourseModuleHomework", {
            courseId, moduleId
        });

        if (response.status === "success") {
            response.data = reactive(response.data ?? []);

            response.data.comments.forEach((item) => {
                item.unread = ref(item.unread);
                watch(() => item.unread, () => {
                    markCommentAsRead(courseId, moduleId, item.id);
                })
            });

            response.data.tools = {
                async addComment(message) {
                    return doRequest("coursesManager", "addHomeworkComment", {
                        courseId, moduleId, message
                    }).then(res => {
                        if (res.status === "success") {
                            storage.data.comments = storage.data.comments ?? [];
                            storage.data.comments.push(res.data);
                        }
                    });
                },

                async uploadHomework(file) {
                    return doRequest("coursesManager", "addHomeworkSubmission", {
                        courseId, moduleId, file
                    }).then(res => {
                        if (res.status === "success") {
                            storage.data.submissions = storage.data.submissions ?? [];
                            storage.data.submissions.push(res.data);
                        }
                    });
                },

                async downloadHomework(fileHash) {
                    return doRequest("coursesManager", "downloadHomeworkFile", {
                        courseId, moduleId, fileHash
                    }).then(console.log);
                },
            };
        }

        return response;
    }

    async function launchUserCourseModuleTest(courseId, moduleId) {
        return await doRequest("coursesManager", "launchUserCourseModuleTest", {
            courseId, moduleId
        });
    }


    async function getUserCourseModuleTestQuestions(courseId, moduleId, updateMetaData) {
        const response = await doRequest("coursesManager", "getUserCourseModuleTestQuestions", {
            courseId, moduleId
        });

        const updater = (questionId, newValue) => {
            let watcher;

            const checker = async () => {
                const updateResponse = await updateUserCourseModuleTest(
                    courseId, moduleId, questionId, newValue
                );
                if (updateResponse.status === "success") {
                    clearInterval(watcher);
                    updateMetaData();
                }
            }
            watcher = setInterval(checker, 5000);
            checker();
        }

        if (response.status === "success") {
            response.data = response.data ?? [];

            response.data.forEach(item => {
                if (item?.type === "single") {
                    item.singleStorage = "";
                    for (let key in item.options) {
                        if (!item.options.hasOwnProperty(key)) {
                            continue;
                        }
                        if (item.options[key] === true) {
                            item.singleStorage = key;
                            break;
                        }
                    }
                }
            });

            response.data = reactive(response.data);
            response.data.forEach((item, index) => {
                    if (item.type === "many") {
                        watch(item.options, (status) => {
                            updater(index, status)
                        });
                    } else {
                        watch(() => item.singleStorage, (newValue) => {
                            for (let key in item.options) {
                                if (!item.options.hasOwnProperty(key)) {
                                    continue;
                                }
                                item.options[key] = (key === newValue)
                            }
                            updater(index, item.options);
                        });
                    }
                }
            );
        }

        return response;
    }

    async function updateUserCourseModuleTest(courseId, moduleId, questionId, answers) {
        return await doRequest("coursesManager", "updateUserCourseModuleTest", {
            courseId, moduleId, questionId, answers
        });
    }

    async function finishUserCourseModuleTest(courseId, moduleId) {
        return await doRequest("coursesManager", "finishUserCourseModuleTest", {
            courseId, moduleId
        });
    }

    async function reviewUserCourseModuleTest(courseId, moduleId) {
        return await doRequest("coursesManager", "reviewUserCourseModuleTest", {
            courseId, moduleId
        });
    }

    async function loadUserCourseModuleTestMetaOnly(courseId, moduleId) {
        return await doRequest("coursesManager", "getUserCourseModuleTest", {
            courseId, moduleId
        });
    }


    async function loadUserCourseModuleTest(courseId, moduleId, origStorage, metaOnly = false) {
        const response = await loadUserCourseModuleTestMetaOnly(courseId, moduleId);

        if (response.status === "success") {
            if (metaOnly) {
                return response.data;
            }

            response.data.tools = {
                async updateMeta() {
                    const newMeta = await loadUserCourseModuleTestMetaOnly(courseId, moduleId);
                    Object.assign(response.data, newMeta.data);
                    response.data.questions.__refresh();
                },

                async launch() {
                    const launchResponse = await launchUserCourseModuleTest(courseId, moduleId)
                    if (launchResponse.status === "success") {
                        response.data.state = "in_progress";
                        response.data.questions.__refresh();
                    }
                },

                async finish() {
                    const finishResponse = await finishUserCourseModuleTest(courseId, moduleId)
                    if (finishResponse.status === "success") {
                        response.data.state = "idle";
                        response.data.lastAttemptTime = Math.floor(Date.now());
                        response.data.currentTry++;
                        response.data.questions.__refresh();
                        response.data.review.__refresh();

                    }
                },
            };

            response.data.questions = getComputableNode(
                updateRate,
                getUserCourseModuleTestQuestions,
                courseId, moduleId, response.data.tools.updateMeta
            );

            response.data.review = getComputableNode(
                updateRate,
                reviewUserCourseModuleTest,
                courseId, moduleId
            );

        }

        return response;
    }

    async function markMessageAsRead(messageHash) {
        return await doRequest("coursesManager", "markMessageAsRead", {messageHash});
    }

    async function getUnreadMessages() {
        const response = await doRequest("coursesManager", "getUnreadMessages");

        if (response.status === "success") {
            response.data.forEach((item) => {
                item.watched = ref(false);
                watch(item.watched, (newValue) => {
                    markMessageAsRead(item.hash);
                });
            });
        }

        return response;
    }

    let availableCourses = getComputableNode(
        updateRate,
        loadAvailableCourses
    );

    let userCourses = getComputableNode(
        updateRate,
        loadUserCourses
    );

    let unreadMessages = getComputableNode(
        updateRate,
        getUnreadMessages
    );

    const update = function () {
        availableCourses.__refresh();
        userCourses.__refresh();
        unreadMessages.__refresh();
    }

    return {
        availableCourses,
        userCourses,
        unreadMessages,
        update
    };
});