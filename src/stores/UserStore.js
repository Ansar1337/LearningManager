import {defineStore} from 'pinia'
import {computed, ref} from "vue";
import {doRequest} from "@/helpers/NetworkManager.js";
import {getComputableNode} from "@/helpers/SmartCompute.js";

export const useUserStore = defineStore('user', () => {
    const updateRate = 60000;

    // const state = ref({
    //     session: {
    //         userId: null,
    //         userName: null,
    //         loggedIn: null,
    //         role: null,
    //         lastUpdate: Infinity
    //     }
    // });

    const sessionTools = {
        // async checkSessionState() {
        //     const response = await doRequest("sessionManager", "getSession",);
        //     if (response.status === "success") {
        //         state.value.session = response.data;
        //         state.value.session.lastUpdate = Date.now();
        //     }
        // },

        async checkSessionState() {
            return await doRequest("sessionManager", "getSession",);
        },

        async tryToLogIn(login, password) {
            const response = (await doRequest("sessionManager", "tryToLogIn", {
                login,
                password
            }));

            if (response.status === "success") {
                // await sessionTools.checkSessionState();
                session.__refresh();
            }
            return response;
        },

        async tryToLogOut() {
            const response = (await doRequest("sessionManager", "tryToLogOut"));

            if (response.status === "success") {
                // await sessionTools.checkSessionState();
                session.__refresh();
            }
            return response;
        },

        async reserveLogin(login) {
            return (await doRequest("sessionManager", "reserveLogin", {
                login
            }));
        },

        async registerLogin(login, password) {
            return (await doRequest("sessionManager", "registerLogin", {
                login, password
            }));
        }
    };

    const session = getComputableNode(
        updateRate,
        sessionTools.checkSessionState);


    // const session = computed(() => {
    //     if (state.value.session.lastUpdate - Date.now() > updateRate) {
    //         sessionTools.checkSessionState().then();
    //     }
    //     return state.value.session;
    // });

    return {session, sessionTools};
});