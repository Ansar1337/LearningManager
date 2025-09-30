import {defineStore} from 'pinia'
import {ref} from "vue";
import {doRequest} from "@/helpers/NetworkManager.js";

export const useSessionStore = defineStore('session', () => {
    const state = ref({
        userName: null,
        loggedIn: null,
        role: null
    });

    async function checkSessionState() {
        const response = (await doRequest("sessionManager", "getSession",));
        if (response.status === "success") {
            state.value = response.data;
        }
    }

    async function tryToLogIn(login, password) {
        const response = (await doRequest("sessionManager", "tryToLogIn", {
            login,
            password
        }));

        if (response.status === "success") {
            await checkSessionState();
        }
        return response.status === "success";
    }

    async function tryToLogOut() {
        const response = (await doRequest("sessionManager", "tryToLogOut"));

        if (response.status === "success") {
            await checkSessionState();
        }
        return response.status === "success";
    }

    return {state, checkSessionState, tryToLogIn, tryToLogOut}
});