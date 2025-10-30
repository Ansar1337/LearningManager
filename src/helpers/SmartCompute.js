"use strict";
import {computed, reactive, ref} from "vue";

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

function getWrappedObject(cleanValue, namespace = "", refreshWith = () => {
}) {
    let resolvedData = {};
    let promise = {};
    let promises = {};

    return new Proxy(cleanValue, {
        get: (target, prop, receiver) => {
            if (prop === '__refresh') {
                return target[prop] ?? refreshWith;
            }

            if (prop === '__namespace') {
                return namespace;
            }


            if (prop === 'then') {
                return (onFulfilled, onRejected) => {
                    return Promise.resolve(target).then(onFulfilled, onRejected);
                };
            }

            if (resolvedData[prop]) {
                return getWrappedValue(resolvedData[prop]?.value ?? resolvedData[prop]);
            }

            if ((promises[namespace]?.[prop]) && (typeof promises[namespace]?.[prop] !== "function")) {
                return getWrappedValue(promises[namespace][prop]);
            }


            if (target instanceof Promise) {
                if ((String(prop).startsWith('__v_')) || (String(prop).startsWith('Symbol(Symbol'))) {
                    // return target?.prop;
                    return undefined;
                }

                if (prop === 'ready') {
                    return false;
                }

                if (prop === 'valueOf') {
                    return () => 0;
                }

                if (prop === 'toString') {
                    return () => "";
                }

                if (!(promises[namespace]?.[prop])) {
                    namespace += String(prop);
                    promise = target.then(res => {
                        resolvedData = res;
                        if (res) {
                            return getWrappedValue(res[prop] ?? res?.value?.[prop]);
                        }
                    });
                    promises[namespace] = promises[namespace] ?? {};
                    promises[namespace][prop] = promises[namespace]?.[prop] ?? {};
                    promises[namespace][prop] = promise;
                    return getWrappedValue(promise);
                }
            }

            if ((String(prop).startsWith('__v_')) || (String(prop).startsWith('Symbol(Symbol'))) {
                return target?.prop;
            }

            if (prop === 'ready') {
                return true;
            }

            if (target[prop]) {
                return target[prop];
            }

            return Reflect.get(target, prop, receiver);
        }
    });
}

function getWrappedValue(cleanValue, namespace = "", refreshWith = () => {
}) {
    if (typeof cleanValue === "object") {
        return getWrappedObject(cleanValue, namespace, refreshWith);
    } else {
        return getWrappedPrimitive(cleanValue);
    }
}

export function getComputableNode(updateRate, populateWithFunc, ...populateWithArgs) {
    const realStorage = reactive({
        data: getWrappedValue(undefined),
        promiseData: Promise.resolve(),
        deferredValue: null,
        lastUpdate: Infinity
    });

    if ((!populateWithFunc) || (typeof populateWithFunc !== "function")) {
        throw new Error("populateWithFunc should be a function!");
    }
    const fullFiller = (refresh = false) => {
        if ((refresh) || ((realStorage.lastUpdate - Date.now()) > updateRate)) {
            const pendingData = populateWithFunc.apply(this, populateWithArgs.concat(realStorage)).then(
                infoResponse => {
                    if (infoResponse.status === "success") {
                        realStorage.data = getWrappedValue(infoResponse.data, undefined, fullFiller.bind(this, true)); //implement soft merging, not replacing
                    } else {
                        realStorage.data = getWrappedValue(infoResponse, undefined, fullFiller.bind(this, true)); //implement soft merging, not replacing
                        console.log(`${populateWithFunc.name} called with ${populateWithArgs.join("|")} got error: <${infoResponse.data}>`);
                    }
                    realStorage.lastUpdate = Date.now();
                    return realStorage.data;
                }
            );

            if (!refresh) {
                realStorage.data = pendingData;
            }
        }

        return getWrappedValue(realStorage.data, undefined, fullFiller.bind(this, true));
    }

    const result = computed(() => fullFiller());
    result.__refresh = fullFiller.bind(this, true);
    return result || {__refresh: null} || Promise.resolve();
}