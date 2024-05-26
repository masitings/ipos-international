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

export const getSaveSearchModalState = state => state.search.saveSearchModalOpen;
export const getSaveSearchModalDataPoolId = state => state.search.searchCurrentDataPoolId;
export const getAddToRequestState = state => state.search.addToRequestState;
export const getShareListRequestStateBySearchId = (state, id) => state.search.shareListFetchingStateBySearchId[id] || NOT_ASKED;
export const getShareListBySearchId = (state, id) => state.search.shareListBySearchId[id];

// List
const listSelectors =
    mapObject(selectors, (_, selector) =>
        (state, ...params) =>
            selector(state.search, ...params)
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