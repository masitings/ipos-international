/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";

export const selectors = {
    getCurrentPageNumber: state => (state.currentPage) || 1,
    getPageCount: state => (state.pageCount),
    getResultCount: state => (state.resultCount),
    getPageSize: state => (state.pageSize),
    getIdsByPageNumber: (state, page = 0) => state.idsByPages[page] || [],
    getItemById: (state, id) => state.itemsById[id],
    getFetchingStateByPage: (state, page = 0) => state.fetchingStateByPage[page] || NOT_ASKED,
    getFetchingMessageByPage: (state, page = 0) => state.fetchingMessageByPage[page] || null,
    getListParams: state => {
        let params = [];

        let currentPageNumber = selectors.getCurrentPageNumber(state);
        if (currentPageNumber && currentPageNumber !== 1) {
            params.push(['page', currentPageNumber]);
        }

        return params;
    },
    getListParamNames: () => ['page']
};