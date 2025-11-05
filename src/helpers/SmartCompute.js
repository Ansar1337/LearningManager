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
    //здесь храним "приватные" данные обертки, которые будут "замкнуты" на нее

    let realValue = undefined; // здесь пишем реальное значение, чтобы отдавать его в случае, если cleanValue было промисом, который разрешился
    let cachedSubValues = {}; //здесь храним кешированные промисы для подзначений, если к ним было обращение

    return new Proxy(cleanValue, {
        get: (target, prop, receiver) => {

            //если есть попытка обращения к __refresh(), удовлетворяем ее до любых проверок
            //это нужно, если внешний код запрашивает принудительное обновление
            if (prop === '__refresh') {
                if ("value" in target) {
                    return target?.value[prop] ?? refreshWith;
                } else {
                    return target[prop] ?? refreshWith;
                }
            }

            //если обращение идет к then, то не важно, промис у нас, или нет, мы это обрабатываем, как положено
            if (prop === 'then') {
                if ("value" in target) {
                    return (onFulfilled, onRejected) => {
                        return Promise.resolve(target?.value).then(onFulfilled, onRejected);
                    };
                } else {
                    return (onFulfilled, onRejected) => {
                        return Promise.resolve(target).then(onFulfilled, onRejected);
                    };
                }
            }

            //иногда может потребоваться привести значение к примитивному типу
            //обрабатываем это соответственно
            if (prop === Symbol.toPrimitive) {
                return (hint) => {
                    switch (hint) {
                        case 'number':
                            return ((target instanceof Promise) && (!realValue)) ? (0) : (Number(realValue ?? target));
                        case 'string':
                            return ((target instanceof Promise) && (!realValue)) ? ("") : (String(realValue ?? target));
                        case 'default':
                        default:
                            return false;
                    }
                };
            }

            //Остальные Symbol адресуем напрямую
            if ((String(prop).startsWith('Symbol(Symbol'))) {
                //в зависимости от структуры объекта, адресуемся либо из него либо из его value(если объект это vue-ref)
                //здесь и далее такие проверки несут тот же смысл
                if ("value" in target) {
                    return getWrappedValue(Reflect.get(target?.value, prop, receiver?.value));
                } else {
                    return getWrappedValue(Reflect.get(target, prop, receiver));
                }
            }

            //затем начинаем проверки
            if (target instanceof Promise) {
                //сразу вешаем хук на резолв target
                target.then(result => {
                    realValue = getWrappedValue(result);
                });

                //если объект, у которого просят значение промис, то значения, скорее всего не существует
                //для начала нужно проверить, был ли этот промис разрешен:
                if (realValue) { //если да, то работаем уже с разрешенным значением
                    //просто адресуем ему запрос напрямую, а там уже будь что будет, нас не волнует
                    if ("value" in target) {
                        return getWrappedValue(Reflect.get(realValue?.value, prop, receiver?.value));
                    } else {
                        return getWrappedValue(Reflect.get(realValue, prop, receiver));
                    }
                }
                //если realValue нет, промис еще не разрешился, проверяем, происходит ли запрос к несуществующему методу/свойству (ведь у промисов есть и собственные)
                if (!(prop in target)) { // если свойства в объекте нет, обещаем вернуть значение, когда он появится
                    //то есть нужно вернуть заглушку, которая разрезолвится
                    //этой заглушкой может быть либо заранее закешированное значение
                    if (String(prop) in cachedSubValues) {
                        return cachedSubValues[prop];
                        //при этом отдаем либо запрошенное свойство, либо его же, но через value, если это был vue-ref
                    }
                    //либо сгенерированное впервые
                    //однако, покуда мы готовы вернуть промис для любого несуществующего значения, мы можем нарваться на ситуацию, когда первый же рекурсивный алгоритм попытается обойти наше генерируемое в реальном времени бесконечное дерево
                    //Vue, например, любит рекурсивно искать "свои" свойства в объектах
                    //задетектить "плохую" рекурсию мы не сможем, поэтому проще пресекать переборы vue-related свойств
                    if (String(prop).startsWith('__v_')) {
                        if ("value" in target) {
                            return getWrappedValue(Reflect.get(target?.value, prop, receiver?.value));
                        } else {
                            return getWrappedValue(Reflect.get(target, prop, receiver));
                        }
                    }
                    //помним, что target[prop] - это вложенное значение, но код здесь вызывается в скоупе target
                    //поэтому вернуть нужно промис, ассоциированный именно с ожидаемым значением
                    const value = new Promise(async (resolve, reject) => { // экзекьютор промиса делаем асинхронным, так как промис и так асинхронен, а async открывает нам доступ к await, а это удобно
                        //здесь нужно дождаться, пока target, будучи промисом, не разрезолвится во что-то
                        const result = await target; // разрешаем наш target
                        realValue = getWrappedValue(result); //сохраняем обернутую версию в замыкании
                        //realValue мы не заворачиваем в обертку, так как оно должно корректно обрабатывать вызовы к несуществующим свойствам и возвращать undefined
                        resolve(getWrappedValue(result?.value?.[prop]?.value ?? result?.[prop]?.value ?? result?.value?.[prop] ?? result?.[prop] ?? false)); //резолвим отданный когда-то промис-заглушку с полученным выше значением
                        //этот механизм должен позволять создавать "виртуальные" цепочки промисов, не требуя .then.then.then
                        // теперь, в цепочке real.virtual.virtual.virtual первый virtual будет превращен в real
                        //  real.virtual.virtual.virtual -> real.real.virtual.virtual
                        //после этого  await target сработает уже для него, и так далее, пока вся цепочка не разрезолвится
                        //при этом промис будет создан на каждый подвызов, то есть, если в реальности цепочка такова:
                        //  Promise[0].attr1.Promise.data
                        //она будет представлена как
                        //  Promise(Promise).Promise([0]).Promise(attr1).Promise(Promise).Promise(data);
                        //С "цепным" резолвом слева-направо
                    });
                    //Здесь есть проблема - при каждом подвызове даже одного и того же свойства будет генерироваться новый независимый промис
                    //этого можно избежать, "кешируя" промисы подвызовов в корневом объекте, и отдавая уже прегенеренные промисы
                    cachedSubValues[String(prop)] = getWrappedValue(value);
                    //поэтому сгенеренный промис мы кладем в объект-хранилище, из которого будем отдавать его по возможности
                    //так мы гарантируем отсутствие ситуации, когда 2 запроса одного подсвойства возвращают разные значения-промисы, да еще и с разной разрешимостью(а такое тоже бывает)
                    return cachedSubValues[String(prop)]; // возвращаем промис, который, теоретически, содержит нужные данные, оборачивая его в такой же прокси
                } else {
                    //если свойство есть, то просто проксируем вызов к оригиналу
                    // return Reflect.get(target, prop, receiver);
                    if (prop in target) {
                        if ((typeof target[prop] === "object") && ("value" in target[prop])) {
                            return getWrappedValue(target[prop].value);
                        } else {
                            return getWrappedValue(target[prop]);
                        }
                    } else if ("value" in target) {
                        if (prop in target.value) {
                            if ((typeof target.value[prop] === "object") && ("value" in target.value[prop])) {
                                return getWrappedValue(target.value[prop].value ?? undefined);
                            } else {
                                return getWrappedValue(target.value[prop] ?? undefined);
                            }
                        } else {
                            return getWrappedValue(target?.[prop] ?? undefined);
                        }
                    } else {
                        return undefined;
                    }
                }
            } else {
                //если target - не промис, обращаемся напрямую, тоже с развертыванием value на разных уровнях
                if (prop in target) {
                    if ((typeof target[prop] === "object") && ("value" in target[prop])) {
                        return getWrappedValue(target[prop].value);
                    } else {
                        return getWrappedValue(target[prop]);
                    }
                } else if ("value" in target) {
                    if (prop in target.value) {
                        if ((typeof target.value[prop] === "object") && ("value" in target.value[prop])) {
                            return getWrappedValue(target.value[prop].value ?? undefined);
                        } else {
                            return getWrappedValue(target.value[prop] ?? undefined);
                        }
                    } else {
                        return getWrappedValue(target?.[prop] ?? undefined);
                    }
                } else {
                    return undefined;
                }
            }
        }
    });
}

function getWrappedValue(cleanValue, namespace = "", refreshWith = () => {
}) {
    if (typeof cleanValue === "object") {
        return getWrappedObject(cleanValue, namespace, refreshWith);
    } else {
        return cleanValue;//getWrappedPrimitive(cleanValue);
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