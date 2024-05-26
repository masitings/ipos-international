/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from "react";
import {getConfig, showError} from "~portal-engine/scripts/utils/general";
import * as api from "~portal-engine/scripts/features/download/download-api";
import {createAction} from "@reduxjs/toolkit";
import {
    getDownloadAttributeById,
    getDownloadListItemById,
    getDownloadListPageNumber
} from "~portal-engine/scripts/features/selectors";
import {createFetchActions} from "~portal-engine/scripts/utils/fetch";
import {mapObject} from "~portal-engine/scripts/utils/utils";
import {createActionCreators} from "~portal-engine/scripts/sliceHelper/list/list-actions";
import {selectors} from "~portal-engine/scripts/sliceHelper/list/list-selectos";
import {fetchTasks} from "~portal-engine/scripts/features/tasks/tasks-actions";
import {singleDownload} from "~portal-engine/scripts/features/download/download-api";
import {showNotification} from "~portal-engine/scripts/features/notifications/notifications-actions";
import * as NOTIFICATION_TYPES from "~portal-engine/scripts/consts/notification-types";
import {getItemById} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import {downloadSelectionStateToConfig} from "~portal-engine/scripts/features/download/download-utils";

export const closeDownloadConfigModal = createAction('download/modal-closed');

// download types
export const {
    actionTypes: DOWNLOAD_TYPES,
    actionCreator: requestDownloadTypes
} = createFetchActions('download/dataTypes', function (state, {dataPoolId}) {
    let publicShareHash = getConfig('publicShare.hash');

    return publicShareHash
        ? api.fetchPublicShareDownloadTypes({
            dataPoolId,
            publicShareHash
        }).response
        : api.fetchDownloadTypes({dataPoolId}).response;
});


// add to cart
export const ADD_TO_CART_CLICKED = 'download/cart/add-to/clicked';
export const addToCartClicked = ({ids, dataPoolId}) => ({
    type: ADD_TO_CART_CLICKED,
    payload: {
        ids: ids,
        dataPoolId: dataPoolId
    }
});

export const {
    actionTypes: ADDED_TO_CART_TYPES,
    actionCreator: addToCart
} = createFetchActions(
    'download/cart/add-to',
    (state, {dataPoolId, ids, selectedIds, formatsById, setupsById}) => {
        let configs = downloadSelectionStateToConfig({selectedIds, formatsById, setupsById, dataPoolId, state});

        let payload = {
            dataPoolId,
            selectedIds: ids,
            configs: configs
        };

        let request = api.addToCart(payload);

        request.then(function (response) {
            showNotification({
                type: NOTIFICATION_TYPES.SUCCESS,
                translation: "download.cart.added",
                action: response.data.url || null,
                actionLabelTranslated: "download.cart.open"
            });
        }).catch(function (error) {
            showError(error)
        });

        return request;
    }
);


// edit cart item
export const EDIT_CART_ITEM_CLICKED = 'download/cart/update-item/clicked';
export const editCartItemClicked = ({id}) => {
    return function (dispatch, getState) {
        const state = getState();
        let item = getDownloadListItemById(state, id);

        dispatch({
            type: EDIT_CART_ITEM_CLICKED,
            payload: {
                id: id,
                dataPoolId: item.dataPoolId
            }
        })
    }
};

export const {
    actionTypes: UPDATED_CART_ITEM_SAVE_TYPES,
    actionCreator: updateCartItem
} = createFetchActions(
    'download/cart/update-item',
    (state, {ids, dataPoolId, formatsById, setupsById, selectedIds}) => {
        let configs = selectedIds.map(attributeId => {
            const {type, localized, attribute, formats} = getDownloadAttributeById(state, {
                id: attributeId,
                dataPoolId
            });

            return {
                type,
                localized,
                attribute,
                format: (formats && formats.length)
                    ? (formatsById[attributeId] || formats[0].id)
                    : null,
                setup: (setupsById && setupsById[attributeId]) ? setupsById[attributeId] : null
            }
        });

        let params = {
            id: ids[0],
            configs: configs
        };

        let request = api.updatedCartItem(params);

        request.then(function () {
            showNotification({
                type: NOTIFICATION_TYPES.SUCCESS,
                translation: "download.cart.updated"
            });
        }).catch(function (error) {
            showError(error);
        });

        return request;
    }
);


// remove from cart
export const {
    actionTypes: REMOVE_FROM_CART_TYPES,
    actionCreator: removeFormCart
} = createFetchActions(
    'download/cart/remove',
    function (state, {id, page}) {
        return api.removeFormCart({id, page});
    }, (state, payload) => ({
        ...payload,
        page: getDownloadListPageNumber(state)
    })
);


// Direct download
export const directDownloadClicked = createAction(
    'download/direct-download/clicked',
    ({ids, dataPoolId, attributes}) => ({
        payload: {
            ids: ids,
            dataPoolId: dataPoolId,
            attributes: attributes
        }
    })
);

export const DIRECT_DOWNLOAD_SINGLE = 'download/direct-download/single';
export const directDownload = (payload) =>
    (dispatch, getState) => {
        const state = getState();

        let {ids, selectedIds, formatsById, setupsById, dataPoolId} = payload;
        let configs = downloadSelectionStateToConfig({
            selectedIds: selectedIds,
            formatsById: formatsById,
            setupsById,
            dataPoolId,
            state
        });

        if (payload.ids.length === 1) {
            let id = ids[0];

            singleDownload({
                id,
                dataPoolId: payload.dataPoolId,
                configs
            });

            dispatch({
                type: DIRECT_DOWNLOAD_SINGLE,
                payload
            });
        } else {
            dispatch(requestDirectMultiDownload({
                ids, dataPoolId, configs
            }));
        }
    };

export const collectionDownload = (payload) =>
    (dispatch, getState) => {
        let {selectedIds, formatsById, setupsById, dataPoolId} = payload;
        let state = getState();
        let configs = downloadSelectionStateToConfig({
            selectedIds: selectedIds,
            formatsById: formatsById,
            setupsById,
            dataPoolId,
            state
        });
        let collectionId = getConfig('collection.id');

        dispatch(requestCollectionDownload({
            collectionId, dataPoolId, configs
        }));

    };

// Direct download - Multi
const {
    actionTypes: MULTI_DOWNLOAD_ESTIMATION_REQUESTED,
    actionCreator: requestDirectMultiDownload,
} = createFetchActions(
    'download/direct-download/multi/estimation',
    (state, {ids, dataPoolId, configs}, dispatch) => (
        new Promise(((resolve, reject) =>
            api.triggerMultiDownloadEstimation({ids, dataPoolId, configs})
                .then(({tmpStoreKey}) => {
                    const poll = () => api.getEstimationResult({tmpStoreKey}).then((payload) => {
                        if (payload.finished) {
                            resolve({
                                tmpStoreKey,
                                ...payload
                            });

                            if (!payload.triggerMessageType) {
                                dispatch(downloadMultipleByTmpStoreKey({tmpStoreKey}));
                            }
                        } else {
                            let timeout = setTimeout(function () {
                                poll();
                                clearTimeout(timeout);
                            }, 1000);
                        }
                    });

                    poll();
                })
                .catch(reject)))
    )
);
export {MULTI_DOWNLOAD_ESTIMATION_REQUESTED};

// Collection Downlaod
const {
    actionTypes: COLLECTION_DOWNLOAD_ESTIMATION_REQUESTED,
    actionCreator: requestCollectionDownload,
} = createFetchActions(
    'download/collection-download/estimation',
    (state, {collectionId, dataPoolId, configs}, dispatch) => (
        new Promise(((resolve, reject) =>
            api.triggerCollectionDownloadEstimation({collectionId, dataPoolId, configs})
                .then(({tmpStoreKey}) => {
                    const poll = () => api.getEstimationResult({tmpStoreKey}).then((payload) => {
                        if (payload.finished) {
                            resolve({
                                tmpStoreKey,
                                ...payload
                            });

                            if (!payload.triggerMessageType) {
                                dispatch(downloadCollection({tmpStoreKey}));
                            }
                        } else {
                            let timeout = setTimeout(function () {
                                poll();
                                clearTimeout(timeout);
                            }, 1000);
                        }
                    });

                    poll();
                })
                .catch(reject)))
    )
);
export {COLLECTION_DOWNLOAD_ESTIMATION_REQUESTED};

export const COLLECTION_DOWNLOAD_CANCELED = 'download/collection-download/canceled';
export const cancelCollectionDownload = createAction(COLLECTION_DOWNLOAD_CANCELED);

export const {
    actionTypes: MULTI_DOWNLOAD_TYPES,
    actionCreator: downloadMultipleByTmpStoreKey,
} = createFetchActions(
    'download/direct-download/multi/download',
    (state, {tmpStoreKey}, dispatch) => {
        let triggerDownloadRequest = api.downloadByTmpStoreKey({tmpStoreKey});

        triggerDownloadRequest.then(() => dispatch(fetchTasks()));

        return triggerDownloadRequest;
    }
);

export const MULTI_DOWNLOAD_CANCELED = 'download/direct-download/multi/canceled';
export const cancelMultiDownload = createAction(MULTI_DOWNLOAD_CANCELED);

// List
export const {
    ACTION_TYPES: DOWNLOAD_CART_LIST_TYPES,
    actionCreators: listActionCreators,
} = createActionCreators({
    actionTypePrefix: 'download/cart',
    api,
    selectors: mapObject(selectors, (_, selector) => (state, ...params) => selector(state.download, ...params))
});

export const {
    urlChanged,
    setup,
    changePage,
    requestListPage
} = listActionCreators;


// Download
const {
    actionTypes: DOWNLOAD_CART_ESTIMATION_FETCHING_TYPES,
    actionCreator: getCartDownloadEstimation,
} = createFetchActions(
    'download/cart/estimation',
    (state, payload, dispatch) => (
        new Promise(((resolve, reject) =>
            api.triggerCartDownloadEstimation()
                .then(({tmpStoreKey}) => {
                    const poll = () => api.getEstimationResult({tmpStoreKey}).then((payload) => {
                        if (payload.finished) {
                            resolve({
                                tmpStoreKey,
                                ...payload
                            });

                            if (!payload.triggerMessageType) {
                                dispatch(downloadCart({tmpStoreKey}));
                            }
                        } else {
                            let timeout = setTimeout(function () {
                                poll();
                                clearTimeout(timeout);
                            }, 1000);
                        }
                    });

                    poll();
                })
                .catch(reject)))
    )
);

export {DOWNLOAD_CART_ESTIMATION_FETCHING_TYPES};
export const downloadCartClicked = getCartDownloadEstimation;

export const {
    actionTypes: DOWNLOAD_CART_DOWNLOAD_TYPES,
    actionCreator: downloadCart,
} = createFetchActions(
    'download/cart/download',
    (state, {tmpStoreKey}, dispatch) => {
        let triggerDownloadRequest = api.downloadByTmpStoreKey({tmpStoreKey});

        triggerDownloadRequest.then(() => dispatch(fetchTasks()));

        return triggerDownloadRequest;
    }
);
export const {
    actionTypes: DOWNLOAD_COLLECTON_DOWNLOAD_TYPES,
    actionCreator: downloadCollection,
} = createFetchActions(
    'download/collection/download',
    (state, {tmpStoreKey}, dispatch) => {
        let triggerDownloadRequest = api.downloadByTmpStoreKey({tmpStoreKey});

        triggerDownloadRequest.then(() => dispatch(fetchTasks()));

        return triggerDownloadRequest;
    }
);

export const DOWNLOAD_CART_CANCELED = 'download/cart/canceled';
export const cancelCartDownload = createAction(DOWNLOAD_CART_CANCELED);

// cart clear
export const {
    actionTypes: DOWNLOAD_CART_CLEARED_TYPES,
    actionCreator: clearCart,
} = createFetchActions(
    'download/cart/clear',
    () => api.clearCart()
);

export const COLLECTION_MULTI_DOWNLOAD_CLICKED = 'download/collection/multi-download';
export const collectionMultiDownloadClicked = (dataPoolId) => {
    return function (dispatch, getState) {
        dispatch({
            type: COLLECTION_MULTI_DOWNLOAD_CLICKED,
            payload: {
                dataPoolId: dataPoolId
            }
        })
    }
};

// public share
export const publicShareDownloadClicked = createAction('download/public-share/clicked');

export const {
    actionTypes: PUBLIC_SHARE_DOWNLOAD_ESTIMATION,
    actionCreator: publicShareDownload,
} = createFetchActions(
    'download/public-share/estimation',
    (state, {dataPoolId, selectedIds, formatsById, setupsById}, dispatch) => {
        const publicShareHash = getConfig('publicShare.hash');

        let configs = downloadSelectionStateToConfig({
            selectedIds: selectedIds,
            formatsById: formatsById,
            setupsById,
            dataPoolId,
            state
        });

        return (
            new Promise(((resolve, reject) =>
                api.triggerPublicShareDownloadEstimation({publicShareHash, dataPoolId, configs})
                    .then(({tmpStoreKey}) => {
                        const poll = () => api.getEstimationResult({tmpStoreKey}).then((payload) => {
                            if (payload.finished) {
                                resolve({
                                    tmpStoreKey,
                                    ...payload
                                });

                                if (!payload.triggerMessageType) {
                                    dispatch(downloadPublicShareByTmpStoreKey({tmpStoreKey}));
                                }
                            } else {
                                let timeout = setTimeout(function () {
                                    poll();
                                    clearTimeout(timeout);
                                }, 1000);
                            }
                        });

                        poll();
                    })
                    .catch(reject)))
        );
    }
);

export const {
    actionTypes: DOWNLOAD_PUBLIC_SHARE_DOWNLOAD_TYPES,
    actionCreator: downloadPublicShareByTmpStoreKey,
} = createFetchActions(
    'download/public-share/download',
    (state, {tmpStoreKey}, dispatch) => {
        let triggerDownloadRequest = api.downloadByTmpStoreKey({tmpStoreKey});

        triggerDownloadRequest.then(() => dispatch(fetchTasks()));

        return triggerDownloadRequest;
    }
);

export const cancelPublicShareDownload = createAction('download/public-share/cancel');