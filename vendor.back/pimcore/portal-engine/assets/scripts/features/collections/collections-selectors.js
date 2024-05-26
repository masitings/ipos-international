/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


// Add to modal
import {mapObject} from "~portal-engine/scripts/utils/utils";
import {selectors} from "~portal-engine/scripts/sliceHelper/list/list-selectos";
import {NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";

export const getAddToModalState = state => state.collections.addToModalOpen;
export const getAddToModalIds = state => state.collections.addToDataIds;
export const getAddToModalDataPoolId = state => state.collections.addToDataPoolId;
export const getAddToRequestState = state => state.collections.addToRequestState;
export const getShareListRequestStateByCollectionId = (state, id) => state.collections.shareListFetchingStateByCollectionId[id] || NOT_ASKED;
export const getShareListByCollectionId = (state, id) => state.collections.shareListByCollectionId[id];
export const getCollectionDetailActionsFetchingState = (state) => state.collections.collectionDetailActionsState || NOT_ASKED;
export const getCollectionDetailActions = (state) => state.collections.collectionDetailActions;

// List
const listSelectors =
    mapObject(selectors, (_, selector) =>
        (state, ...params) =>
            selector(state.collections, ...params)
    );

export const {
    getCurrentPageNumber,
    getPageCount,
    getResultCount,
    getPageSize,
    getIdsByPageNumber,
    getItemById,
    getFetchingStateByPage,
    getFetchingMessageByPage,
    getListParams,
    getListParamNames,
} = listSelectors;