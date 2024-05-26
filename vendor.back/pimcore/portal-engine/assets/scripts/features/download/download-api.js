/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {addParamsObjectToURL, addParamTupleArrayToURL} from "~portal-engine/scripts/utils/utils";
import {abortAbleFetch, prepareFetchPromise} from "~portal-engine/scripts/utils/fetch";
import {getLanguage} from "~portal-engine/scripts/utils/intl";
import {getConfig} from "~portal-engine/scripts/utils/general";

const publicShareHash = getConfig('publicShare.hash');
const additionalParams = {
    ...(publicShareHash ? {publicShareHash} : null)
};

export function fetchDownloadTypes({dataPoolId}) {
    let {abort, response} = abortAbleFetch(addParamsObjectToURL(`/_portal-engine/api/download/download-types`, {
        ...additionalParams,
        dataPoolId
    }));

    return {
        abort,
        response: prepareFetchPromise(response)
    }
}
export function fetchPublicShareDownloadTypes({dataPoolId, publicShareHash}) {
    let {abort, response} = abortAbleFetch(addParamsObjectToURL(`/_portal-engine/api/public-share/download/download-types`, {
        ...additionalParams,
        dataPoolId,
        publicShareHash
    }));

    return {
        abort,
        response: prepareFetchPromise(response)
    }
}

export function fetchList(params) {
    let {abort, response} = abortAbleFetch(
        addParamsObjectToURL(
            addParamTupleArrayToURL(`/_portal-engine/api/download/download-cart`, params),
            {
                ...additionalParams,
                documentId: getConfig('documentId')
            }
        )
    );

    return {
        abort,
        response: prepareFetchPromise(response)
    }
}

export const addToCart = ({dataPoolId, selectedIds, configs}) => prepareFetchPromise(
    fetch(addParamsObjectToURL(`/_portal-engine/api/download/add-to-download-cart`, {
        documentId: getConfig('documentId'),
        dataPoolId,
        'selectedIds[]': selectedIds
    }), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            configs
        })
    })
);

export const updatedCartItem = ({id, configs}) => (
    prepareFetchPromise(
        fetch(`/_portal-engine/api/download/update-download-cart?itemKey=${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                configs
            })
        })
    )
);

export const removeFormCart = ({id, ...params}) =>
    prepareFetchPromise(fetch(addParamsObjectToURL(`/_portal-engine/api/download/remove-from-download-cart`, {itemKey: id, ...params})));


export const downloadByTmpStoreKey = ({tmpStoreKey}) =>
    prepareFetchPromise(
        fetch(addParamsObjectToURL(`/_portal-engine/api/download/trigger-download`, {
            ...additionalParams,
            tmpStoreKey,
            _locale: getLanguage()
        }))
    );


export const clearCart = () => prepareFetchPromise(fetch(`/_portal-engine/api/download/clear-cart`));

export const triggerCartDownloadEstimation = () => prepareFetchPromise(fetch(`/_portal-engine/api/download/download-cart-trigger-download-estimation`));

export const triggerCollectionDownloadEstimation = ({collectionId, dataPoolId, configs}) => prepareFetchPromise(fetch(
    addParamsObjectToURL(
        `/_portal-engine/api/download/collection-trigger-download-estimation`, {
            ...additionalParams,
            collectionId,
            dataPoolId,
        },
    ), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            configs
        })
    }));

export const triggerPublicShareDownloadEstimation = ({publicShareHash, dataPoolId, configs}) =>
    prepareFetchPromise(fetch(
        addParamsObjectToURL(
            `/_portal-engine/api/public-share/download/trigger-download-estimation`, {
                ...additionalParams,
                publicShareHash,
                dataPoolId,
            },
        ), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                configs
            })
        }));


export const triggerMultiDownloadEstimation = ({ids, dataPoolId, configs}) => prepareFetchPromise(fetch(
    addParamsObjectToURL(
        `/_portal-engine/api/download/multi-download-trigger-download-estimation?dataPoolId=210`, {
            ...additionalParams,
            'ids[]': ids,
            dataPoolId,
        },
    ), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            configs
        })
    }));

export const getEstimationResult = ({tmpStoreKey}) => prepareFetchPromise(
    fetch(addParamsObjectToURL(`/_portal-engine/api/download/get-estimation-result`, {
        ...additionalParams,
        tmpStoreKey,
        _locale: getLanguage(),
        _: new Date().getTime(),
    }))
);

import download from "downloadjs";
import {showNotification} from "~portal-engine/scripts/features/notifications/notifications-actions";
import * as NOTIFICATION_TYPES from "~portal-engine/scripts/consts/notification-types";

export const singleDownload = ({id, dataPoolId, configs, fileName = "download"}) => (
    fetch(
        addParamsObjectToURL(
            `/_portal-engine/api/download/single-download/${id}`, {
                ...additionalParams,
                dataPoolId
            }
        ), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                configs
            })
        }).then(function (response) {
            const contentType = response.headers.get('content-type');
            const contentDisposition = response.headers.get('Content-Disposition');
            if (contentType && contentType.includes('application/json') && (!contentDisposition || !contentDisposition.includes('attachment'))) {
                response.json().then(payload => showNotification({
                    type: NOTIFICATION_TYPES.ERROR,
                    translation: "download.no-downloadable-files"
                }))
            } else {
                let filename = contentDisposition.split("filename=")[1].replace(/["]+/g, '');
                response.blob().then(function (blob) {
                    download(blob, filename)
                });
            }
        }
    )
);

export function directAssetDownload(id, thumbnail) {
    window.open(
        addParamsObjectToURL(`/_portal-engine/api/asset/download/${id}`, {
            ...additionalParams,
            dataPoolId: getConfig("currentDataPool.id"),
            thumbnail: thumbnail ? thumbnail : 0
        })
    );
}