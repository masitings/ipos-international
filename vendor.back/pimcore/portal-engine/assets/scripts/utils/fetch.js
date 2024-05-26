/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {FAILED, FETCHING, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import {getConfig, showError} from "~portal-engine/scripts/utils/general";
import {isPlainObject} from "~portal-engine/scripts/utils/utils";

const endpoints = {};

export function setEndpoint(key, url) {
    endpoints[key] = url;
}


if (getConfig("endpoints")) {
    Object.entries(getConfig("endpoints")).forEach(([key, endpoint]) => {
        setEndpoint(key, endpoint);
    });
}

export function getEndpoint(key) {
    if (!endpoints[key]) {
        console.error(`Missing api endpoint "${key}"`);
        return ''
    }

    return endpoints[key];
}

export function buildParams(data) {
    const params = new URLSearchParams();

    for (const [key, value] of Object.entries(data)) {
        if (Array.isArray(value)) {
            value.map((item) => {
                params.append(`${key}[]`, item);
            })
        } else {
            params.append(key, value);
        }
    }

    return params;
}

export function fetchJson(url, data = {}, ignoreSuccess = false) {
    return fetch(url, data)
        .then(res => res.json())
        .then(json => {
            if (!ignoreSuccess && !json.success) {
                return Promise.reject(json.error);
            }

            return json;
        });
}

export function sendJson(url, json) {
    return fetchJson(url, {
        method: "POST",
        body: JSON.stringify(json),
        headers: {
            "Content-Type": "application/json; charset=UTF-8"
        }
    });
}

export function abortAbleFetch(url, data = {}) {
    const controller = new AbortController();
    const signal = controller.signal;

    let response = fetch(url, {
        ...data,
        signal
    });

    return {
        abort: () => controller.abort(),
        response
    };
}

export function errorToPayload(error) {
    if (typeof error !== "string") {
        return {};
    }

    return {
        payload: errorToObject(error)
    }
}

export function errorToObject(error) {
    if (typeof error !== "string") {
        return {};
    }

    return {
        error: error
    }
}

export function fetchingStateReducer(requested, fetched, failed, fetchingState, errorState, payloadMapper) {
    return {
        [requested]: function (state) {
            state[fetchingState] = FETCHING;
        },

        [fetched]: function (state, {payload}) {
            state[fetchingState] = SUCCESS;
            payloadMapper(state, payload);
        },

        [failed]: function (state, {payload}) {
            state[fetchingState] = FAILED;

            if (typeof payload === "object") {
                state[errorState] = payload.error;
            }
        }
    }
}

// todo rename fnc
export function createFetchActions(actionTypePrefix, fnc, mapStateToActionPayload, abortMultiple = true) {
    let pendingRequest;

    const TYPES = {
        REQUESTED: `${actionTypePrefix}/requested`,
        SUCCEEDED: `${actionTypePrefix}/succeeded`,
        FAILED: `${actionTypePrefix}/failed`,
    };

    return {
        actionTypes: TYPES,
        actionCreator: (params) => (dispatch, getState) => {
            const state = getState();

            if (abortMultiple && pendingRequest && pendingRequest.abort) {
                pendingRequest.abort();
            }

            let payload = (mapStateToActionPayload && typeof mapStateToActionPayload === "function")
                ? mapStateToActionPayload(state, params)
                : params;

            if (!isPlainObject(payload)) {
                // make sure to not pass any non-serializable payload to redux
                payload = {};
            }

            dispatch({type: TYPES.REQUESTED, payload: payload});

            pendingRequest = fnc(state, payload, dispatch);

            let promise = (pendingRequest.response && typeof pendingRequest.response.then === "function")
                ? pendingRequest.response
                : pendingRequest;

            promise.then(function ({success, error, ...remainingPayload}) {
                pendingRequest = null;

                if (success) {
                    dispatch({
                        type: TYPES.SUCCEEDED,
                        payload: {
                            ...payload,
                            ...remainingPayload
                        }
                    });
                } else {
                    // todo dev only
                    return Promise.reject(error || `No success field in response`);
                }
            }).catch((error) => {
                if (error.name === 'AbortError') {
                    return;
                }

                pendingRequest = null;

                showError(error);

                dispatch({
                    type: TYPES.FAILED,
                    payload: {
                        ...payload,
                        error: error ? error.toString() : null
                    }

                })
            });

            return promise;
        }
    }
}

export function prepareFetchPromise(fetchPromise) {
    return fetchPromise
        .then(response => response.json())
        .then(response => {
            if (response.redirectUrl) {
                window.location.href = response.redirectUrl;
            }

            return response;
        })
        .catch(payload => {
            return payload.error
                ? Promise.reject(payload.error)
                : Promise.reject(payload)
        })
}