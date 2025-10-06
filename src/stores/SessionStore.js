import {defineStore} from 'pinia'
import {ref} from "vue";
import {doRequest} from "@/helpers/NetworkManager.js";

export const useSessionStore = defineStore('session', () => {
    const state = ref({
        userId: null,
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
        return response;
    }

    async function tryToLogOut() {
        const response = (await doRequest("sessionManager", "tryToLogOut"));

        if (response.status === "success") {
            await checkSessionState();
        }
        return response;
    }

    async function reserveLogin(login) {
        return (await doRequest("sessionManager", "reserveLogin", {
            login
        }));
    }

    async function registerLogin(login, password) {
        return (await doRequest("sessionManager", "registerLogin", {
            login, password
        }));
    }

    return {state, checkSessionState, reserveLogin, registerLogin, tryToLogIn, tryToLogOut};
});