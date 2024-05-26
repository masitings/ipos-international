/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {trans} from "~portal-engine/scripts/utils/intl";
import {showNotification} from "~portal-engine/scripts/features/notifications/notifications-actions";
import {ERROR} from "~portal-engine/scripts/consts/notification-types";
import {useState} from "react";

let config = null;

function loadConfig() {
    if (config !== null) {
        return config;
    }

    config = {};
    const element = document.getElementById("js-frontend-config");

    if (!element) {
        return config;
    }

    try {
        config = JSON.parse(element.innerText);
    } catch (exception) {
        console.error(exception);
        config = {};
    }
}

export function getConfig(path) {
    loadConfig();

    if (!path) {
        return config;
    }

    let parts = path.split(".");

    if (!parts.length) {
        return config;
    }

    let current = config;

    parts.forEach((part) => {
        if (current && typeof current === "object") {
            current = current[part];
        }
    });

    return current;
}

export const GENERAL_ERROR_KEY = "general-error";

export function showError(error, isTranslationKey = false) {
    if (error && error.name === 'AbortError') {
        return;
    }

    if (!error) {
        error = GENERAL_ERROR_KEY;
        isTranslationKey = true;
    }

    if (isTranslationKey) {
        trans(error).then((translated) => {
            showNotification({
                message: translated.toString(),
                type: ERROR
            });
            console.error(error, translated);
        });
    } else {
        showNotification({
            message: error.toString(),
            type: ERROR
        });
        console.error(error);
    }
}

// todo use abortAbleFetch?
export function makePromiseAbortable(p) {
    let aborted = false;

    const promise = new Promise((resolve, reject) => {
        p.then(
            (...args) => aborted ? reject({aborted: true}) : resolve(...args),
            (...args) => aborted ? reject({aborted: true}) : reject(...args)
        );
    });

    return {
        promise,
        abort() {
            aborted = true;
        }
    }
}