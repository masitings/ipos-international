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
import {arrayToObject} from "~portal-engine/scripts/utils/utils";
import {
    TAGS_FETCHING_TYPES,
    toggleTagCollapseState,
    UNFILTERED_TAGS_FETCHING_TYPES
} from "~portal-engine/scripts/features/tags/tags-actions";
import {fetchingStateReducer} from "~portal-engine/scripts/utils/fetch";

const initialState = {
    fetchingState: NOT_ASKED,
    error: null,
    byId: {},
    allIds: [],
    selectedIds: [],
    fetchingStateUnfiltered: NOT_ASKED,
    errorUnfiltered: null,
    unfilteredIds: [],
};

export default createReducer(initialState, {
    ...fetchingStateReducer(
        TAGS_FETCHING_TYPES.REQUESTED,
        TAGS_FETCHING_TYPES.SUCCEEDED,
        TAGS_FETCHING_TYPES.FAILED,
        'fetchingState',
        'error',
        (state, {data: {items}}) => {
            let normalizedItems = normalize(items);
            state.byId = {
                ...state.byId,
                ...arrayToObject(normalizedItems, 'id')
            };
            state.allIds = normalizedItems.map(({id}) => id)
        }
    ),
    ...fetchingStateReducer(
        UNFILTERED_TAGS_FETCHING_TYPES.REQUESTED,
        UNFILTERED_TAGS_FETCHING_TYPES.SUCCEEDED,
        UNFILTERED_TAGS_FETCHING_TYPES.FAILED,
        'fetchingStateUnfiltered',
        'errorUnfiltered',
        (state, {data: {items}}) => {
            let normalizedItems = normalize(items);
            state.byId = {
                ...state.byId,
                ...arrayToObject(normalizedItems, 'id')
            };
            state.unfilteredIds = normalizedItems.map(({id}) => id)
        }
    ),
    [toggleTagCollapseState]: function (state, {payload: {id, state: currentState}}) {
        state.byId[id].isOpen = currentState;
    }
});

function normalize(items = [], parentId = null) {
    return items.flatMap(function ({items, expanded, ...node}) {
        return [
            {
                ...node,
                isOpen: expanded,
                parent: parentId,
            },
            ...(items ? normalize(items, node.id) : [])
        ];
    })
}