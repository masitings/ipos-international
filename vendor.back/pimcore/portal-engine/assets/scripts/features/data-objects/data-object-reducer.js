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
import {NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import {
    DETAIL_FAILED,
    DETAIL_FETCHED,
    DETAIL_REQUESTED,
    SETUP,
    VERSION_COMPARISON_FAILED,
    VERSION_COMPARISON_FETCHED,
    VERSION_COMPARISON_REQUESTED,
    VERSION_LIST_FAILED,
    VERSION_LIST_FETCHED,
    VERSION_LIST_REQUESTED,
    VERSION_SELECTION_TOGGLED
} from "~portal-engine/scripts/features/data-objects/data-object-actions";
import {fetchingStateReducer} from "~portal-engine/scripts/utils/fetch";

const initialState = {
    dataObjectId: null,
    dataPoolId: null,
    versionsEnabled: false,
    detail: null,
    permissions: {},
    validLanguages: null,
    detailFetchingState: NOT_ASKED,
    detailError: null,
    versionHistoryUrl: null,
    versionsFetchingState: NOT_ASKED,
    versionsError: null,
    versionHistory: [],
    versionComparisonFetchingState: NOT_ASKED,
    versionComparisonError: null,
    versionComparison: {},
    selectedVersionIds: []
};

export default createReducer(initialState, {
    [SETUP]: function (state, {payload: {dataObjectId, dataPoolId, versionsEnabled}}) {
        state.dataObjectId = dataObjectId;
        state.dataPoolId = dataPoolId;
        state.versionsEnabled = versionsEnabled;
    },

    ...fetchingStateReducer(DETAIL_REQUESTED, DETAIL_FETCHED, DETAIL_FAILED, "detailFetchingState", "detailError", function(state, payload) {
        state.detail = {data: payload.detail.data, breadcrumbs: payload.detail.breadcrumbs};
        state.permissions = payload.detail.permissions;
        state.validLanguages = payload.validLanguages;
    }),

    ...fetchingStateReducer(VERSION_LIST_REQUESTED, VERSION_LIST_FETCHED, VERSION_LIST_FAILED, "versionsFetchingState", "versionsError", function(state, payload) {
        state.versionHistory = payload;
    }),

    ...fetchingStateReducer(VERSION_COMPARISON_REQUESTED, VERSION_COMPARISON_FETCHED, VERSION_COMPARISON_FAILED, "versionComparisonFetchingState", "versionComparisonError", function(state, payload) {
        state.versionComparison = payload;
    }),

    [VERSION_SELECTION_TOGGLED]: function (state, {payload: {id, isSelected}}) {
        if (isSelected) {
            state.selectedVersionIds.push(id);
        } else {
            state.selectedVersionIds = state.selectedVersionIds.filter(currentId => currentId !== id);
        }
    }
});