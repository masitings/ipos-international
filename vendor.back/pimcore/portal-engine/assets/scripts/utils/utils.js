/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import * as icons from "~portal-engine/icons";

export const noop = () => {};
export const identity = x => x;

export const truthy = x => !!x;

export const arrayToObject = (array, keyField = "id") =>
    array.reduce((obj, item) => {
        obj[item[keyField]] = item;
        return obj
    }, {});

export const mapObject = (object, mapper) => (
    Object.entries(object).reduce((acc, [key, value]) => ({
        ...acc,
        [key]: mapper(key, value)
    }), {})
);

export const addParamsObjectToURL = (url, params = {}) =>
    url + (url.indexOf('?') >= 0 ? '&' : '?') + serializeParamsObject(params);

export const addParamsArrayToURL = (url, params = []) =>
    url + (url.indexOf('?') >= 0 ? '&' : '?') + serializeParamsArray(params);

export const serializeParamsObject = (params = {}) =>
    Object.entries(params)
        .flatMap(([key, value]) => Array.isArray(value)
            ? value.map(singleValue => ([key, singleValue]))
            : [[key, value]])
        .map(([key, value]) => `${key}=${value}`)
        .join('&');

export const addParamTupleArrayToURL = (url, params = []) =>
    url + (url.indexOf('?') >= 0 ? '&' : '?') + serializeParamTupleArray(params);

/* Array with name & value-attributes */
export const serializeParamsArray = (params = []) =>
    params.map(({name, value}) => `${name}=${value}`).join('&');

/* Array with [name, value] tuples */
export const serializeParamTupleArray = (params = []) =>
    params.map(([name, value]) => `${name}=${value}`).join('&');

export const deserializeParamsToObject = (params = []) => (
    params.reduce((acc, [key, value]) => {
        if (key.match(/\[.*]/g)) {
            // array params
            let name = key.replace(/\[.*]/g, '');
            return ({
                ...acc,
                [name]: [...(acc[name] || []), value]
            });
        } else {
            return ({
                ...acc,
                [key]: value
            });
        }
    }, {})
);

export const removeDuplicatesFromArray = array => Array.from(new Set(array));

export function sortParamTuples([key1, value1], [key2, value2]) {
    let entry1 = `${key1}/${value1}`;
    let entry2 = `${key2}/${value2}`;

    if (entry1 < entry2) {
        return -1;
    }

    if (entry1 > entry2) {
        return 1;
    }

    return 0;
}

// Component Helper
export const getIconComponentByName = name => icons[name];

export const removeDuplicates = array => Array.from(new Set(array));

export const curry = (func) => {
    return function curried(...args) {
        if (args.length >= func.length) {
            return func.apply(this, args);
        } else {
            return function(...args2) {
                return curried.apply(this, args.concat(args2));
            }
        }
    };
};

export function isPlainObject(obj) {
    if (typeof obj !== 'object' || obj === null) return false;
    var proto = Object.getPrototypeOf(obj);
    if (proto === null) return true;
    var baseProto = proto;

    while (Object.getPrototypeOf(baseProto) !== null) {
        baseProto = Object.getPrototypeOf(baseProto);
    }

    return proto === baseProto;
}

export function isValidHttpUrl(string) {
    let url;

    try {
        url = new URL(string);
    } catch (_) {
        return false;
    }

    return url.protocol === "http:" || url.protocol === "https:";
}

export function downloadFromUrl({url, filename}) {
    const a = document.createElement('a');
    a.href = url;
    a.download = filename || 'download';

    // Click handler that releases the object URL after the element has been clicked
    // This is required for one-off downloads of the blob content
    const clickHandler = () => {
        setTimeout(() => {
            URL.revokeObjectURL(url);
            a.removeEventListener('click', clickHandler);
        }, 150);
    };

    a.addEventListener('click', clickHandler, false);

    a.click();
}

export function getObjectFromLocalStorage(key) {
    try {
        let stringValue = localStorage.getItem(key);

        if (stringValue) {
            return JSON.parse(stringValue);
        }
    } catch (e) {
        return null;
    }
}

// todo use everywhere
export function setObjectToLocalStorage(key, object) {
    try {
        localStorage.setItem(key, JSON.stringify(object));
        return true;
    } catch (e) {
        return false;
    }
}
