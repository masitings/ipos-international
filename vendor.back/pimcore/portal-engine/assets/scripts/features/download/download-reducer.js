/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {createReducer} from "@reduxjs/toolkit";
import {
    ADD_TO_CART_CLICKED,
    ADDED_TO_CART_TYPES,
    closeDownloadConfigModal,
    DOWNLOAD_TYPES,
    UPDATED_CART_ITEM_SAVE_TYPES,
    EDIT_CART_ITEM_CLICKED,
    REMOVE_FROM_CART_TYPES,
    DOWNLOAD_CART_LIST_TYPES,
    directDownloadClicked,
    MULTI_DOWNLOAD_ESTIMATION_REQUESTED,
    DIRECT_DOWNLOAD_SINGLE,
    DOWNLOAD_CART_DOWNLOAD_TYPES,
    DOWNLOAD_CART_CLEARED_TYPES,
    DOWNLOAD_CART_ESTIMATION_FETCHING_TYPES,
    DOWNLOAD_CART_CANCELED,
    MULTI_DOWNLOAD_CANCELED,
    COLLECTION_DOWNLOAD_ESTIMATION_REQUESTED,
    COLLECTION_MULTI_DOWNLOAD_CLICKED,
    COLLECTION_DOWNLOAD_CANCELED,
    DOWNLOAD_COLLECTON_DOWNLOAD_TYPES,
    MULTI_DOWNLOAD_TYPES,
    publicShareDownloadClicked,
    cancelPublicShareDownload,
    DOWNLOAD_PUBLIC_SHARE_DOWNLOAD_TYPES, PUBLIC_SHARE_DOWNLOAD_ESTIMATION
} from "~portal-engine/scripts/features/download/download-actions";
import {arrayToObject} from "~portal-engine/scripts/utils/utils";
import {FAILED, FETCHING, NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import {MODAL_MODES} from "~portal-engine/scripts/features/download/dowload-consts";
import {
    createReducer as createListReducer,
    initialState as initialListState
} from "~portal-engine/scripts/sliceHelper/list/list-reducer";
import {getConfigModalMode} from "~portal-engine/scripts/features/download/download-selectors";
import {DEFAULT_PAGE} from "~portal-engine/scripts/consts";
import {FILE_SIZE_TO_BIG} from "~portal-engine/scripts/consts/download-message-types";

let listSliceReducer = createListReducer({
    ACTION_TYPES: DOWNLOAD_CART_LIST_TYPES,
    payloadMapper: ({data}) => ({
        data: {
            ...data,
            entries: (data.entries || []).map(normalizedDownloadCartEntry)
        }
    })
});

function normalizedDownloadCartEntry(entry) {
    return {
        ...entry,
        configsById: arrayToObject(
            entry.configs.map(config => ({...config, attributeId: getAttributeId(config)})),
            'attributeId'
        )
    }
}


const initialState = {
    configModalOpen: false,
    configModalIds: [],
    configModalDataPoolId: null,
    configModalAttributes: [],
    configModalMode: null,

    // types
    configFetchErrorByDataPoolId: {},
    configFetchStateByDataPoolId: {},
    // type attributes
    attributesByDataPool: {},

    // cart download messages
    cartDownloadFetchingMode: NOT_ASKED,
    cartDownloadMessageType: null,
    cartDownloadMessageText: null,
    cartDownloadMessageTmpStoreKey: null,

    // download messages
    multiDownloadFetchingState: NOT_ASKED,
    multiDownloadMessageType: null,
    multiDownloadMessageText: null,
    multiDownloadMessageTmpStoreKey: null,

    // collection download messages
    collectionDownloadFetchingState: NOT_ASKED,
    collectionDownloadMessageType: null,
    collectionDownloadMessageText: null,
    collectionDownloadMessageTmpStoreKey: null,

    // public share download messages
    publicShareDownloadFetchingState: NOT_ASKED,
    publicShareDownloadMessageType: null,
    publicShareDownloadMessageText: null,
    publicShareDownloadMessageTmpStoreKey: null,

    // cart list
    ...initialListState
};

export default createReducer(initialState, {
    [DOWNLOAD_TYPES.REQUESTED]: (state, {payload: {dataPoolId}}) => {
        state.configFetchErrorByDataPoolId[dataPoolId] = null;
        state.configFetchStateByDataPoolId[dataPoolId] = FETCHING;
    },
    [DOWNLOAD_TYPES.SUCCEEDED]: (state, {payload: {dataPoolId, data}}) => {
        state.configFetchStateByDataPoolId[dataPoolId] = SUCCESS;

        let normalizedData = normalize(data);

        state.attributesByDataPool[dataPoolId] = state.attributesByDataPool[dataPoolId] || {
            byId: {},
            allIds: []
        };

        state.attributesByDataPool[dataPoolId].byId = {
            ...state.attributesByDataPool[dataPoolId].byId,
            ...normalizedData.attributesById
        };
        state.attributesByDataPool[dataPoolId].allIds = normalizedData.allAttributeIds;
    },
    [DOWNLOAD_TYPES.FAILED]: (state, {payload}) => {
        // todo ?
        console.warn('todo failed', payload);
    },

    [closeDownloadConfigModal]: function (state) {
        state.configModalOpen = false;
    },
    [ADD_TO_CART_CLICKED]: function (state, {payload}) {
        state.configModalMode = MODAL_MODES.ADD;
        setOpenModalState(state, payload);
    },
    [directDownloadClicked]: function (state, {payload}) {
        state.configModalMode = MODAL_MODES.DOWNLOAD;
        setOpenModalState(state, payload);
    },
    [publicShareDownloadClicked]: function (state, {payload}) {
        state.configModalMode = MODAL_MODES.DOWNLOAD_PUBLIC_SHARE;
        setOpenModalState(state, payload);
    },
    [DIRECT_DOWNLOAD_SINGLE]: function (state) {
        state.configModalOpen = false;
    },

    /* multi download */
    [MULTI_DOWNLOAD_ESTIMATION_REQUESTED.REQUESTED]: function (state) {
        state.multiDownloadFetchingState = FETCHING;
    },
    [MULTI_DOWNLOAD_ESTIMATION_REQUESTED.SUCCEEDED]: function (state, {payload}) {
        state.multiDownloadMessageText = payload.triggerMessage || null;
        state.multiDownloadMessageType = payload.triggerMessageType || null;
        state.multiDownloadMessageTmpStoreKey = payload.tmpStoreKey || null;

        if (!payload.triggerMessageType || payload.triggerMessageType === FILE_SIZE_TO_BIG) {
            state.multiDownloadFetchingState = SUCCESS;
        }

        if (getConfigModalMode(state) === MODAL_MODES.DOWNLOAD) {
            state.configModalOpen = false;
        }
    },
    [MULTI_DOWNLOAD_ESTIMATION_REQUESTED.FAILED]: function (state) {
        state.multiDownloadFetchingState = FAILED;
    },
    [MULTI_DOWNLOAD_CANCELED]: function (state) {
        state.multiDownloadMessageText = null;
        state.multiDownloadMessageType = null;
        state.multiDownloadMessageTmpStoreKey = null;
        state.multiDownloadFetchingState = NOT_ASKED;
    },
    [MULTI_DOWNLOAD_TYPES.SUCCEEDED]: function (state) {
        state.multiDownloadMessageText = null;
        state.multiDownloadMessageType = null;
        state.multiDownloadMessageTmpStoreKey = null;
        state.multiDownloadFetchingState = NOT_ASKED;
    },

    /* cart download */
    [DOWNLOAD_CART_ESTIMATION_FETCHING_TYPES.REQUESTED]: function (state) {
        state.cartDownloadFetchingMode = FETCHING;
    },
    [DOWNLOAD_CART_ESTIMATION_FETCHING_TYPES.SUCCEEDED]: function (state, {payload}) {
        state.cartDownloadMessageText = payload.triggerMessage || null;
        state.cartDownloadMessageType = payload.triggerMessageType || null;
        state.cartDownloadMessageTmpStoreKey = payload.tmpStoreKey || null;

        if (!payload.triggerMessageType || payload.triggerMessageType === FILE_SIZE_TO_BIG) {
            state.cartDownloadFetchingMode = SUCCESS;
        }
    },
    [DOWNLOAD_CART_DOWNLOAD_TYPES.REQUESTED]: function (state) {
        state.cartDownloadMessageText = null;
        state.cartDownloadMessageType = null;
        state.cartDownloadMessageTmpStoreKey = null;
    },
    [DOWNLOAD_CART_DOWNLOAD_TYPES.SUCCEEDED]: function (state) {
        state.cartDownloadFetchingMode = SUCCESS;
        state.cartDownloadMessageText = null;
        state.cartDownloadMessageType = null;
        state.cartDownloadMessageTmpStoreKey = null;
    },
    [DOWNLOAD_CART_DOWNLOAD_TYPES.FAILED]: function (state) {
        state.cartDownloadFetchingMode = FAILED;
    },
    [DOWNLOAD_CART_DOWNLOAD_TYPES.REQUESTED]: function (state) {
        state.multiDownloadMessageText = null;
        state.multiDownloadMessageType = null;
        state.multiDownloadMessageTmpStoreKey = null;
    },
    [DOWNLOAD_CART_CANCELED]: function (state) {
        state.cartDownloadMessageText = null;
        state.cartDownloadMessageType = null;
        state.cartDownloadMessageTmpStoreKey = null;
        state.cartDownloadFetchingMode = NOT_ASKED;
    },
    [ADDED_TO_CART_TYPES.REQUESTED]: function (state) {
        state.configModalOpen = false;
    },
    [EDIT_CART_ITEM_CLICKED]: function (state, {payload: {dataPoolId, id}}) {
        state.configModalMode = MODAL_MODES.EDIT;
        setOpenModalState(state, {dataPoolId, ids: [id]});
    },
    [UPDATED_CART_ITEM_SAVE_TYPES.REQUESTED]: function (state) {
        state.configModalOpen = false;
        // todo optimistic update?
    },
    [UPDATED_CART_ITEM_SAVE_TYPES.SUCCEEDED]: function (state, {payload: {data}}) {
        state.itemsById[data.id] = normalizedDownloadCartEntry(data);
    },
    [REMOVE_FROM_CART_TYPES.SUCCEEDED]: function (state, action) {
        state.idsByPages = {};
        state.fetchingStateByPage = {};

        listSliceReducer[DOWNLOAD_CART_LIST_TYPES.PAGE_FETCH_SUCCEEDED](state, action)
    },
    [DOWNLOAD_CART_CLEARED_TYPES.REQUESTED]: function (state) {
        state.idsByPages = {};
        // state.fetchingStateByPage = {};
        state.currentPage = DEFAULT_PAGE;
        state.pageCount = 0;
        state.resultCount = 0;
    },
    [DOWNLOAD_CART_CLEARED_TYPES.FAILED]: function (state) {
        state.fetchingStateByPage = {};
    },
    [COLLECTION_MULTI_DOWNLOAD_CLICKED]: function (state, {payload}) {
        state.configModalMode = MODAL_MODES.DOWNLOAD_COLLECTION;
        setOpenModalState(state, payload);
    },


    [DOWNLOAD_COLLECTON_DOWNLOAD_TYPES]: function (state) {
        state.configModalOpen = false;
    },

    // collection download
    [COLLECTION_DOWNLOAD_ESTIMATION_REQUESTED.REQUESTED]: function (state) {
        state.collectionDownloadFetchingState = FETCHING;
    },
    [COLLECTION_DOWNLOAD_ESTIMATION_REQUESTED.SUCCEEDED]: function (state, {payload}) {
        state.collectionDownloadMessageText = payload.triggerMessage || null;
        state.collectionDownloadMessageType = payload.triggerMessageType || null;
        state.collectionDownloadMessageTmpStoreKey = payload.tmpStoreKey || null;

        if (!payload.triggerMessageType || payload.triggerMessageType === FILE_SIZE_TO_BIG) {
            state.collectionDownloadFetchingState = SUCCESS;
        }

        if (getConfigModalMode(state) === MODAL_MODES.DOWNLOAD_COLLECTION) {
            state.configModalOpen = false;
        }
    },
    [DOWNLOAD_COLLECTON_DOWNLOAD_TYPES.REQUESTED]: function (state) {
        state.collectionDownloadMessageText = null;
        state.collectionDownloadMessageType = null;
        state.collectionDownloadMessageTmpStoreKey = null;
    },
    [DOWNLOAD_COLLECTON_DOWNLOAD_TYPES.SUCCEEDED]: function (state) {
        state.collectionDownloadFetchingState = SUCCESS;
    },
    [DOWNLOAD_COLLECTON_DOWNLOAD_TYPES.FAILED]: function (state) {
        state.collectionDownloadFetchingState = FAILED;
    },
    [DOWNLOAD_COLLECTON_DOWNLOAD_TYPES.REQUESTED]: function (state) {
        state.collectionDownloadMessageText = null;
        state.collectionDownloadMessageType = null;
        state.collectionDownloadMessageTmpStoreKey = null;
    },
    [COLLECTION_DOWNLOAD_CANCELED]: function (state) {
        state.collectionDownloadMessageText = null;
        state.collectionDownloadMessageType = null;
        state.collectionDownloadMessageTmpStoreKey = null;
        state.collectionDownloadFetchingState = NOT_ASKED;
    },

    // public share download
    [PUBLIC_SHARE_DOWNLOAD_ESTIMATION.REQUESTED]: function (state) {
        state.publicShareDownloadFetchingState = FETCHING;
    },
    [PUBLIC_SHARE_DOWNLOAD_ESTIMATION.SUCCEEDED]: function (state, {payload}) {
        state.publicShareDownloadMessageText = payload.triggerMessage || null;
        state.publicShareDownloadMessageType = payload.triggerMessageType || null;
        state.publicShareDownloadMessageTmpStoreKey = payload.tmpStoreKey || null;

        if (!payload.triggerMessageType || payload.triggerMessageType === FILE_SIZE_TO_BIG) {
            state.publicShareDownloadFetchingState = SUCCESS;
        }

        if (getConfigModalMode(state) === MODAL_MODES.DOWNLOAD_PUBLIC_SHARE) {
            state.configModalOpen = false;
        }
    },
    [DOWNLOAD_PUBLIC_SHARE_DOWNLOAD_TYPES.REQUESTED]: function (state) {
        state.publicShareDownloadMessageText = null;
        state.publicShareDownloadMessageType = null;
        state.publicShareDownloadMessageTmpStoreKey = null;
    },
    [DOWNLOAD_PUBLIC_SHARE_DOWNLOAD_TYPES.SUCCEEDED]: function (state) {
        state.publicShareDownloadFetchingState = SUCCESS;
    },
    [DOWNLOAD_PUBLIC_SHARE_DOWNLOAD_TYPES.FAILED]: function (state) {
        state.publicShareDownloadFetchingState = FAILED;
    },
    [cancelPublicShareDownload]: function (state) {
        state.publicShareDownloadMessageText = null;
        state.publicShareDownloadMessageType = null;
        state.publicShareDownloadMessageTmpStoreKey = null;
        state.publicShareDownloadFetchingState = NOT_ASKED;
    },


    ...listSliceReducer,
});

function normalize(data = []) {
    let attributes = data.map(entry => ({...entry, id: getAttributeId(entry)}));

    return {
        attributesById: arrayToObject(attributes),
        allAttributeIds: attributes.map(({id}) => id)
    }
}

export function getAttributeId({id, type, attribute}) {
    let keys = [type, attribute].filter(x => !!x);
    return id || keys.join('-');
}

function setOpenModalState(state, {ids, dataPoolId, attributes}) {
    state.configModalOpen = true;
    state.configModalIds = ids;
    state.configModalDataPoolId = dataPoolId;
    state.configModalAttributes = attributes;
}