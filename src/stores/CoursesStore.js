import {defineStore} from 'pinia'
import {computed, isRef, ref, watch} from "vue";
import {doRequest} from "@/helpers/NetworkManager.js";


export const useCoursesStore = defineStore('courses', () => {

    // let eagerMode = ref(false);
    let updateRate = 60000;

    class ProxiedPrimitive {
        constructor(value) {
            this._value = value;
            return new Proxy(this, {
                get(target, prop, receiver) {
                    if (prop === 'then') {
                        return (onFulfilled, onRejected) => {
                            return Promise.resolve(target._value).then(onFulfilled, onRejected);
                        };
                    }

                    if (prop === 'valueOf') {
                        return () => target._value;
                    } else if (prop === 'toString') {
                        return () => String(target._value);
                    }
                    return Reflect.get(target, prop, receiver);
                },
                set(target, prop, value) {
                    if (prop === '_value') {
                        console.log(`Значение изменено с ${target._value} на ${value}`);
                        target._value = value;
                        return true;
                    }
                    return Reflect.set(target, prop, value);
                }
            });
        }
    }

    function getWrappedValue(cleanValue, namespace = "") {
        let resolvedData = undefined;
        let promise = {};
        let promises = {};

        if (typeof cleanValue === "object") {
            return new Proxy(cleanValue, {
                get: (target, prop, receiver) => {
                    namespace += String(prop);

                    if (prop === '__v_isRef') {
                        return target?.__v_isRef;
                    }

                    if (prop === 'then') {
                        return (onFulfilled, onRejected) => {
                            return Promise.resolve(target).then(onFulfilled, onRejected);
                        };
                    }

                    if ((promises[namespace]?.[prop]) && (typeof promises[namespace]?.[prop] !== "function")) {
                        return getWrappedValue(promises[namespace][prop], namespace);
                    }

                    if ((target instanceof Promise) && (!(promises[namespace]?.[prop]))) {
                        promise = target.then(res => {
                            resolvedData = res;
                            if (res) {
                                return getWrappedValue(res[prop] ?? res?.value?.[prop], namespace);
                            }
                        });
                        promises[namespace] = promises[namespace] ?? {};
                        promises[namespace][prop] = promises[namespace]?.[prop] ?? {};
                        promises[namespace][prop] = promise;
                        return getWrappedValue(promise, namespace);
                    }

                    return Reflect.get(target, prop, receiver);
                }
            });
        } else {
            return new ProxiedPrimitive(cleanValue);
        }
    }

    function getComputableNode(populateWithFunc, ...populateWithArgs) {
        const realStorage = ref({
            data: getWrappedValue([]),
            promiseData: Promise.resolve(),
            deferredValue: null,
            lastUpdate: Infinity
        });
        const fullFiller = () => {
            if ((realStorage.value.lastUpdate - Date.now()) > updateRate) {

                realStorage.value.data = populateWithFunc.apply(this, populateWithArgs.concat(realStorage)).then(
                    infoResponse => {
                        if (infoResponse.status === "success") {
                            realStorage.value.data = getWrappedValue(infoResponse.data); //implement soft merging, not replacing
                            realStorage.value.lastUpdate = Date.now();
                        } else {
                            console.log(`${populateWithFunc.name} called with ${populateWithArgs.join("|")} got error: <${infoResponse.data}>`);
                        }
                        return realStorage.value.data;
                    }
                );
            }

            return getWrappedValue(realStorage.value.data);
        }

        return computed(fullFiller) || Promise.resolve();
    }

    /*
    new Proxy(infoResponse.data, {
                                get: function (target, prop) {
                                    if (prop === 'then' && !(prop in target)) {
                                        return () => {
                                            console.log("фейковый then");
                                            return Promise.resolve(infoResponse.data);
                                        };
                                    }

                                    return Reflect.get(...arguments);
                                }
                            })
     */

    async function loadAvailableCourses() {
        const coursesResponse = await doRequest("coursesManager", "getAvailableCourses");
        if (coursesResponse.status === "success") {
            for (let i = 0; i < coursesResponse.data.length; i++) {
                coursesResponse.data[i].details = getComputableNode(
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

    function forceLoad() {

    }

    const availableCourses = getComputableNode(
        loadAvailableCourses
    );

    const userCourses = getComputableNode(
        loadUserCourses
    );

    return {
        availableCourses,
        userCourses
    };
});