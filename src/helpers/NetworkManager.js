"use strict";

export class Storage {
    constructor() {
        this.data = {};
        this.callbacks = {};
    }
}

export class CUD {
    constructor(storage, type) {
        this.storage = storage;
        this.type = type;
    }

    get(name) {
        return this.storage[this.type][name];
    }

    set(name, value) {
        if (!this.storage.hasOwnProperty(this.type)) {
            this.storage[this.type] = {};
        }
        this.storage[this.type][name] = value;
    }

    remove(name) {
        delete this.storage[this.type][name];
    }

    list() {
        return this.storage[this.type];
    }

    clear() {
        this.storage[this.type] = {};
    }
}

export class Payload {
    constructor(data = {}) {
        this.storage = new Storage();
        this.status = "raw";
        this.data = new CUD(this.storage, 'data');
        this.callbacks = {
            'prelude': new CUD(this.storage.callbacks, 'prelude'),
            'success': new CUD(this.storage.callbacks, 'success'),
            'error': new CUD(this.storage.callbacks, 'error'),
            'final': new CUD(this.storage.callbacks, 'final'),
        };

        for (let key in data) {
            if (data.hasOwnProperty(key)) {
                this.data.set(key, data[key]);
            }
        }
    }

    pack() {
        let rawData = this.data.list();
        let packet = new FormData();

        for (let key in rawData) {
            if (rawData.hasOwnProperty(key)) {
                packet.append(key, rawData[key]);
            }
        }

        this.data.clear();
        this.data.set("data", packet);
        this.status = "packed";
        return this;
    }

    finalize() {
        let result = new Storage();
        result.data = new FormData();

        let rawData = this.data.list();
        for (let key in rawData) {
            if (rawData.hasOwnProperty(key)) {
                if (rawData[key] instanceof FormData) {
                    for (let pair of rawData[key].entries()) {
                        result.data.append(`${key}[${pair[0]}]`, pair[1]);
                    }
                } else if (typeof rawData[key] === "string") {
                    result.data.append(key, rawData[key]);
                } else {
                    result.data.append(key, JSON.stringify(rawData[key]));
                }
            }
        }

        for (let callbackType in this.callbacks) {
            if (this.callbacks.hasOwnProperty(callbackType)) {
                let rawCallbacks = this.callbacks[callbackType].list();
                result.callbacks[callbackType] = function (data) {
                    for (let key in rawCallbacks) {
                        if (rawCallbacks.hasOwnProperty(key)) {
                            rawCallbacks[key](data);
                        }
                    }
                }
            }
        }
        return Object.freeze(result);
    }
}

export async function doRequest(actor, action, payload = {}) {

    if (!(payload instanceof Payload)) {
        payload = new Payload(payload);
    }

    payload = payload.pack();
    payload.data.set("actor", actor);
    payload.data.set("action", action);
    payload = payload.finalize();
    const url = import.meta.env.VITE_API_SERVER_URL || "https://rtlm.tableer.com";

    let response = await fetch(url, {
        method: 'POST',
        credentials: "include",
        body: payload.data
    });

    const contentType = response.headers.get('Content-Type');

    if (contentType.includes('json')) {
        return await response.json();
    } else if (contentType.includes('octet-stream')) {

        const contentDisposition = response.headers.get('Content-Disposition');
        let filename = 'default_name';

        if (contentDisposition) {
            const filenameMatch = contentDisposition.match(/filename="?(.+)"?/);
            if (filenameMatch.length > 1) {
                filename = filenameMatch[1];
            }
        }

        const url = window.URL.createObjectURL(await response.blob());
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    }
}