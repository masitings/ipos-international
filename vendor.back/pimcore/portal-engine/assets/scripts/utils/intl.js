/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {LOAD_CATALOGUE, ADD_KEYS, KEY_BULK_SIZE, PROCESS_QUEUE_INTERVAL} from "~portal-engine/scripts/consts/intl-api";

const defaultDomain = "portal-engine";

let queueTimeout = null;
const queue = [];
const catalogue = {};

let cataloguePromises = {};

export function getLanguage() {
    return document.documentElement.lang || "en";
}

function loadCatalogue(domain) {
    if (catalogue[domain]) {
        // catalogue is already loaded, resolve instantly
        return new Promise(resolve => resolve());
    }

    if (!cataloguePromises[domain]) {
        // no concurrent promise for this domain yet, load the catalogue
        cataloguePromises[domain] = new Promise((resolve) => {
            fetch(`${LOAD_CATALOGUE}${getLanguage()}/${domain}`)
                .then((response) => {
                    return response.json()
                })
                .then((json) => {
                    if (json.success) {
                        catalogue[domain] = json.data || {};
                    }
                })
                .catch(() => {
                    console.error("Could not load translation catalogue for domain", domain);
                    resolve();
                })
                .finally(() => {
                    delete cataloguePromises[domain];
                    resolve();
                });
        });
    }

    return cataloguePromises[domain];
}

function processQueue() {
    const data = queue.splice(0, KEY_BULK_SIZE);

    fetch(ADD_KEYS, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({keys: data})
    }).catch((...args) => {
        console.error("Could not add translation keys");
        console.error(...args);
    });

    if (queue.length) {
        setTimeout(processQueue, PROCESS_QUEUE_INTERVAL)
    } else {
        queueTimeout = null;
    }
}

function addKeyToQueue(key, domain) {
    // do not add default domain, will be added automatically
    queue.push({
        key: key,
        domain: domain === defaultDomain ? null : domain
    });

    catalogue[domain][key] = null;

    if (queueTimeout === null) {
        queueTimeout = setTimeout(processQueue, PROCESS_QUEUE_INTERVAL);
    }
}

export function translateKey(key, domain = defaultDomain, params) {
    const fallback = `${key}`;

    if (!hasTrans(key, domain)) {
        // key does not exist yet, add it to the creation queue
        addKeyToQueue(key, domain);
        return fallback;
    }

    // todo params?
    let translated = fallback;

    if (catalogue[domain][key]) {
        translated = catalogue[domain][key];
    }

    return translated;
}

export function hasTrans(key, domain) {
    const domainCatalogue = catalogue[domain];

    if (!domainCatalogue) {
        return false;
    }

    if (domainCatalogue[key] === undefined) {
        return false;
    }

    return true;
}

export function trans(key, domain = defaultDomain, params = {}) {
    return new Promise((resolve) => {
        loadCatalogue(domain)
            .then(() => {
                resolve(translateKey(key, domain, params));
            });
    });
}