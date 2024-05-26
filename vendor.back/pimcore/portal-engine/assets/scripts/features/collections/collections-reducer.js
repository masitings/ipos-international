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
    addToCollectionClicked,
    closeAddToModal,
    ADDED_TO_COLLECTION_TYPES,
    COLLECTION_LIST_TYPES,
    RENAMED_COLLECTION_TYPES,
    CREATED_COLLECTION_TYPES,
    DELETED_COLLECTION_TYPES,
    ADDED_TO_NEW_COLLECTION_TYPES,
    COLLECTION_SHARE_LIST_REQUEST_TYPES,
    UPDATE_COLLECTION_SHARE_LIST_TYPES,
    COLLECTION_DETAIL_ACTIONS_TYPES
} from "~portal-engine/scripts/features/collections/collections-actions";
import {FAILED, FETCHING, NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import {
    createReducer as createListReducer,
    initialState as initialListState
} from "~portal-engine/scripts/sliceHelper/list/list-reducer";

const initialState = {
    // list
    ...initialListState,

    addToModalOpen: false,
    addToDataIds: [],
    addToDataPoolId: null,
    addToRequestState: NOT_ASKED,

    // share
    shareListByCollectionId: {},
    shareListFetchingStateByCollectionId: {},

    // collection detail actions
    collectionDetailActionsState: NOT_ASKED,
};

let listSliceReducer = createListReducer({
    ACTION_TYPES: COLLECTION_LIST_TYPES,
});

export default createReducer(initialState, {
    ...listSliceReducer,
    /* add to collection */
    [addToCollectionClicked]: (state, {payload: {dataPoolId, ids}}) => {
        state.addToModalOpen = true;
        state.addToDataPoolId = dataPoolId;
        state.addToDataIds = ids;
    },
    [closeAddToModal]: state => {
        state.addToModalOpen = false;
    },
    [ADDED_TO_COLLECTION_TYPES.REQUESTED]: state => {
        state.addToRequestState = FETCHING;
    },
    [ADDED_TO_COLLECTION_TYPES.SUCCEEDED]: state => {
        state.addToRequestState = SUCCESS;
        state.addToModalOpen = false;
    },
    [ADDED_TO_COLLECTION_TYPES.FAILED]: state => {
        state.addToRequestState = FAILED;
    },
    [ADDED_TO_NEW_COLLECTION_TYPES.REQUESTED]: state => {
        state.addToRequestState = FETCHING;
    },
    [ADDED_TO_NEW_COLLECTION_TYPES.SUCCEEDED]: state => {
        state.addToRequestState = SUCCESS;
        state.addToModalOpen = false;
    },
    [ADDED_TO_NEW_COLLECTION_TYPES.FAILED]: state => {
        state.addToRequestState = FAILED;
    },

    /* CRUD list*/
    [RENAMED_COLLECTION_TYPES.SUCCEEDED]: (state, {payload: {id, data}}) => {
        state.itemsById[id] = data;
    },
    [CREATED_COLLECTION_TYPES.SUCCEEDED]: (state, {payload: {id, data}}) => {
        // todo only clear pages after & current page
        state.itemsById[data.id] = data;
        state.idsByPages = {};
        state.fetchingStateByPage = {};
    },
    [DELETED_COLLECTION_TYPES.SUCCEEDED]: (state) => {
        // todo only clear pages after & current page
        state.idsByPages = {};
        state.fetchingStateByPage = {};
    },

    /*share*/
    [COLLECTION_SHARE_LIST_REQUEST_TYPES.REQUESTED]: (state, {payload:{collectionId}}) => {
        state.shareListFetchingStateByCollectionId[collectionId] = FETCHING;
    },
    [COLLECTION_SHARE_LIST_REQUEST_TYPES.SUCCEEDED]: (state, {payload: {collectionId, data}}) => {
        state.shareListFetchingStateByCollectionId[collectionId] = SUCCESS;
        state.shareListByCollectionId[collectionId] = data;
    },
    [COLLECTION_SHARE_LIST_REQUEST_TYPES.FAILED]: (state, {payload: {collectionId}}) => {
        state.shareListFetchingStateByCollectionId[collectionId] = FAILED;
    },
    [UPDATE_COLLECTION_SHARE_LIST_TYPES.REQUESTED]: (state, {payload:{collectionId}}) => {
        state.shareListFetchingStateByCollectionId[collectionId] = FETCHING;
    },
    [UPDATE_COLLECTION_SHARE_LIST_TYPES.SUCCEEDED]: (state, {payload: {collectionId, data}}) => {
        state.shareListFetchingStateByCollectionId[collectionId] = SUCCESS;
        state.shareListByCollectionId[collectionId] = data;
    },
    [UPDATE_COLLECTION_SHARE_LIST_TYPES.FAILED]: (state, {payload: {collectionId}}) => {
        state.shareListFetchingStateByCollectionId[collectionId] = FAILED;
    },

    /*detail actions*/
    [COLLECTION_DETAIL_ACTIONS_TYPES.SUCCEEDED]: (state, {payload:{data}}) => {
        state.collectionDetailActionsState = SUCCESS;
        state.collectionDetailActions = data.actions;
    },
});
