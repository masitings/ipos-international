/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {selectors} from "~portal-engine/scripts/sliceHelper/filter-list/filter-list-selectors";
import {mapObject} from "~portal-engine/scripts/utils/utils";

export const dataPoolSelectors =
    mapObject(selectors, (_, selector) =>
        (state, ...params) =>
            selector(state.dataPoolList, ...params)
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
    getAllFilterNames,
    getFilterFetchingState,
    getFilterStatesFetchingState,
    getFilterByName,
    getFilterStateByName,
    getAllFilters,
    getAllVisibleFilters,
    getSelectedFilterValues,
    getSerializedFilterValues,
    getCurrentFilterParams,
    isSelected,
    getAllSelectedIds,
    getSelectedItemsFetchState,
    getSidebarListFetchingState,
    getAllSidebarListIds,
    getSidebarActiveId,
    getCurrentView,
    getNavigationType,
    getSelectedFolderPath,
    getSelectedTagIds,
    isTagSelectedById,
    getListViewAttributes,
    getCurrentOrderBy,
    getOrderByOptions,
    getPermissions,
    getUpdateModalState,
    getUpdateModalIds,
    getUpdateItemModalDataPoolId,
    getUpdateItemModalLoading,
    getUploadFolder,
} = dataPoolSelectors;