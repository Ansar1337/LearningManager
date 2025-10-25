"use strict";
import {computed, ref} from "vue";

function getWrappedPrimitive(cleanValue) {
    let placeholder = {
        _value: cleanValue
    }
    return new Proxy(placeholder, {
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
                target._value = value;
                return true;
            }
            return Reflect.set(target, prop, value);
        }
    });
}

function getWrappedObject(cleanValue, namespace) {
    let resolvedData = undefined;
    let promise = {};
    let promises = {};

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
}

function getWrappedValue(cleanValue, namespace = "") {
    if (typeof cleanValue === "object") {
        return getWrappedObject(cleanValue);
    } else {
        return getWrappedPrimitive(cleanValue);
    }
}

export function getComputableNode(updateRate, populateWithFunc, ...populateWithArgs) {
    const realStorage = ref({
        data: getWrappedValue(undefined),
        promiseData: Promise.resolve(),
        deferredValue: null,
        lastUpdate: Infinity
    });

    if ((!populateWithFunc) || (typeof populateWithFunc !== "function")) {
        throw new Error("populateWithFunc should be a function!");
    }
    const fullFiller = (refresh = false) => {
        if ((refresh) || ((realStorage.value.lastUpdate - Date.now()) > updateRate)) {
            const pendingData = populateWithFunc.apply(this, populateWithArgs.concat(realStorage)).then(
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

            if (!refresh) {
                realStorage.value.data = pendingData;
            }
        }

        return getWrappedValue(realStorage.value.data);
    }

    const result = computed(() => fullFiller());
    result.__refresh = fullFiller.bind(this, true);
    return result || {__refresh: null} || Promise.resolve();
}