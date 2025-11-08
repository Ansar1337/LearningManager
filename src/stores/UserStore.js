import {defineStore} from 'pinia'
import {computed, ref} from "vue";
import {doRequest} from "@/helpers/NetworkManager.js";
import {getComputableNode} from "@/helpers/SmartCompute.js";

export const useUserStore = defineStore('user', () => {
    const updateRate = 60000;
    let callOnChange = [];

    async function loadUserProfile() {
        const response = await doRequest("userManager", "loadProfileData");

        if (response.status === "success") {
            response.data.updateWith = async function (newProfile) {
                const response = await doRequest("userManager", "saveProfileData", {newProfile});
                if (response.status === "success") {
                    session.__refresh();
                }
            }
        }

        return response;
    }

    const sessionTools = {
        addChangingListener(callable) {
            callOnChange.push(callable);
        },

        async loadSessionState() {
            const sessionData = await doRequest("userManager", "getSession");
            if (sessionData.status === "success") {
                sessionData.data.profile = getComputableNode(
                    updateRate,
                    loadUserProfile
                );

                callOnChange.forEach((listener) => {
                    listener();
                })
            }

            return sessionData;
        },

        async tryToLogIn(login, password) {
            const response = (await doRequest("userManager", "tryToLogIn", {
                login,
                password
            }));

            if (response.status === "success") {
                session.__refresh();
            }
            return response;
        },

        async tryToLogOut() {
            const response = (await doRequest("userManager", "tryToLogOut"));

            if (response.status === "success") {
                session.__refresh();
            }
            return response;
        },

        async reserveLogin(login) {
            return (await doRequest("userManager", "reserveLogin", {
                login
            }));
        },

        async registerLogin(login, password) {
            return (await doRequest("userManager", "registerLogin", {
                login, password
            }));
        }
    };

    const session = getComputableNode(
        updateRate,
        sessionTools.loadSessionState);

    return {session, sessionTools};
});