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
    SEARCH_LIST_TYPES,
    SAVE_SEARCH_TYPES,
    DELETED_SEARCH_TYPES,
    SEARCH_SHARE_LIST_REQUEST_TYPES,
    UPDATE_SEARCH_SHARE_LIST_TYPES,
    RENAMED_SEARCH_TYPES
} from "~portal-engine/scripts/features/search/search-actions";
import {FAILED, FETCHING, NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import {
    createReducer as createListReducer,
    initialState as initialListState
} from "~portal-engine/scripts/sliceHelper/list/list-reducer";

const initialState = {
    ...initialListState,

    saveSearchRequestState: NOT_ASKED,

    // share
    shareListBySearchId: {},
    shareListFetchingStateBySearchId: {},
};

let listSliceReducer = createListReducer({
    ACTION_TYPES: SEARCH_LIST_TYPES,
});

export default createReducer(initialState, {
    ...listSliceReducer,

    /* add to search list */
    [SAVE_SEARCH_TYPES.REQUESTED]: state => {
        state.saveSearchRequestState = FETCHING;
    },
    [SAVE_SEARCH_TYPES.SUCCEEDED]: (state, {payload: {id, data}}) => {
        state.itemsById[data.id] = data;
        state.saveSearchRequestState = SUCCESS;
    },
    [SAVE_SEARCH_TYPES.FAILED]: state => {
        state.saveSearchRequestState = FAILED;
    },

    [RENAMED_SEARCH_TYPES.SUCCEEDED]: (state, {payload: {id, data}}) => {
        state.itemsById[id] = data;
    },
    [DELETED_SEARCH_TYPES.SUCCEEDED]: (state) => {
        state.idsByPages = {};
        state.fetchingStateByPage = {};
    },

    /*share*/
    [SEARCH_SHARE_LIST_REQUEST_TYPES.REQUESTED]: (state, {payload:{searchId}}) => {
        state.shareListFetchingStateBySearchId[searchId] = FETCHING;
    },
    [SEARCH_SHARE_LIST_REQUEST_TYPES.SUCCEEDED]: (state, {payload: {searchId, data}}) => {
        state.shareListFetchingStateBySearchId[searchId] = SUCCESS;
        state.shareListBySearchId[searchId] = data;
    },
    [SEARCH_SHARE_LIST_REQUEST_TYPES.FAILED]: (state, {payload: {searchId}}) => {
        state.shareListFetchingStateBySearchId[searchId] = FAILED;
    },
    [UPDATE_SEARCH_SHARE_LIST_TYPES.REQUESTED]: (state, {payload:{searchId}}) => {
        state.shareListFetchingStateBySearchId[searchId] = FETCHING;
    },
    [UPDATE_SEARCH_SHARE_LIST_TYPES.SUCCEEDED]: (state, {payload: {searchId, data}}) => {
        state.shareListFetchingStateBySearchId[searchId] = SUCCESS;
        state.shareListBySearchId[searchId] = data;
    },
    [UPDATE_SEARCH_SHARE_LIST_TYPES.FAILED]: (state, {payload: {searchId}}) => {
        state.shareListFetchingStateBySearchId[searchId] = FAILED;
    },
});
