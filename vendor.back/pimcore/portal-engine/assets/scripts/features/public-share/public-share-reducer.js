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
import {FAILED, FETCHING, NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import {
    closedPublicShareModal,
    GUEST_SHARING_SUBMITTING_TYPES,
    publicShareClicked,
    PUBLIC_SHARE_LIST_TYPES,
    DELETED_PUBLIC_SHARE_TYPES,
    GUEST_SHARING_UPDATED_TYPES,
    PUBLIC_SHARE_COLLECTION_CLICKED
} from "~portal-engine/scripts/features/public-share/public-share-actions";
import {
    createReducer as createListReducer,
    initialState as initialListState
} from "~portal-engine/scripts/sliceHelper/list/list-reducer";
import {getConfig} from "~portal-engine/scripts/utils/general";

const initialState = {
    isPublicShare: !!getConfig('publicShare.hash'),
    showTermsText: !!getConfig('publicShare.showTermsText'),
    termsText: getConfig('publicShare.termsText') || '',

    isModalOpen: false,
    modalShareId: null,
    modalItemIds: [],
    modalCollectionId: null,
    modalDataPools: [],
    submitState: NOT_ASKED,
    submitError: null,

    // list
    ...initialListState,
};

let listSliceReducer = createListReducer({
    ACTION_TYPES: PUBLIC_SHARE_LIST_TYPES,
});


export default createReducer(initialState, {
    ...listSliceReducer,

    [publicShareClicked]: (state, {payload: {ids, dataPool}}) => {
        state.isModalOpen = true;
        state.modalItemIds = ids;
        state.submitState = NOT_ASKED;
        state.modalDataPools = [dataPool];
        state.modalCollectionId = null;
        state.shareUrl = null;
    },
    [PUBLIC_SHARE_COLLECTION_CLICKED]: (state, {payload: {collectionId, dataPools = []}}) => {
        state.isModalOpen = true;
        state.modalItemIds = [];
        state.modalCollectionId = collectionId;
        state.modalDataPools = dataPools;
        state.submitState = NOT_ASKED;
        state.shareUrl = null;
        state.submitError = null;
    },
    [closedPublicShareModal]: (state) => {
        state.isModalOpen = false;
    },
    [GUEST_SHARING_SUBMITTING_TYPES.SUCCEEDED]: (state, {payload: {data: {detailUrl}}}) => {
        state.submitState = SUCCESS;
        state.shareUrl = detailUrl;
    },
    [GUEST_SHARING_SUBMITTING_TYPES.FAILED]: (state, {payload: {error}}) => {
        state.submitState = FAILED;
        state.submitError = error;
    },
    [GUEST_SHARING_SUBMITTING_TYPES.REQUESTED]: (state) => {
        state.submitState = FETCHING;
        state.submitError = null;
    },
    [DELETED_PUBLIC_SHARE_TYPES.SUCCEEDED]: (state) => {
        // todo only clear pages after & current page
        state.idsByPages = {};
        state.fetchingStateByPage = {};
    },
    [GUEST_SHARING_UPDATED_TYPES.SUCCEEDED]: (state, {payload: {id, data}}) => {
        state.itemsById[id] = data;
    }
});