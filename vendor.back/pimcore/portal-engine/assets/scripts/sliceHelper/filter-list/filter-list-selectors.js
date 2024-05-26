/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {selectors as listSelectors} from "~portal-engine/scripts/sliceHelper/list/list-selectos";
import * as filtersByType from "~portal-engine/scripts/components/filter/inputs/index";
import {FOLDERS, TAGS} from "~portal-engine/scripts/consts/list-navigation-types";
import {getConfig} from "~portal-engine/scripts/utils/general";
import {NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import {DEFAULT_PAGE} from "~portal-engine/scripts/consts";
import {TILE} from "~portal-engine/scripts/consts/teaser-list-views";
import {getSelectionKey} from "~portal-engine/scripts/sliceHelper/filter-list/filter-list-utils";

// filters
export const getAllFilterNames = state => state.allFilterNames;
export const getFilterFetchingState = state => state.filterFetchState || NOT_ASKED;
export const getFilterStatesFetchingState = state => state.filterStatesFetchState || NOT_ASKED;
export const getFilterByName = (state, name) => state.filtersByName[name];
export const getFilterStateByName = (state, name) => state.filterStatesByName[name];
export const getAllFilters = (state) => getAllFilterNames(state).map(name => getFilterByName(state, name));

export const getAllVisibleFilters = (state) => {
    return getAllFilterNames(state)
        .map(name => getFilterByName(state, name))
        .filter(filter => getFilterStateByName(state, filter.name).visible);
};

export const getSelectedFilterValues = (state) =>
    getAllVisibleFilters(state)
        .map(filter => {
            if (!filtersByType[filter.type].getSelectedFilterValues) {
                throw new Error(`No getSelectedFilterValues function for filter type ${filter.type}`)
            }

            let filterState = getFilterStateByName(state, filter.name);

            return filtersByType[filter.type].getSelectedFilterValues(filter, filterState)
        })
        .flat()
        .filter(
            filter => !!filter && !!filter.value
        );

export const getSerializedFilterValues = (state) =>
    getAllVisibleFilters(state)
        .map(filter => {
            if (!filtersByType[filter.type].serialize) {
                throw new Error(`No serialize function for filter type ${filter.type}`)
            }

            let filterState = getFilterStateByName(state, filter.name);
            let result = filtersByType[filter.type].serialize(filter, filterState);

            /* make sure we have a nested array to be able to flatten correctly */
            return (Array.isArray(result) && Array.isArray(result[0]))
                ? result
                : [result];
        })
        .flat()
        .filter(
            filter => !!filter && filter.length && !!filter[1]
        );

export const getCurrentFilterParams = (state) =>
    getSerializedFilterValues(state)
        .map(([name, value]) => [encodeURIComponent(name), encodeURIComponent(value)]);


// selection
export const isSelected = (state, {id, dataPoolId, collectionId, publicShareHash}) => {
    let selectionKey = getSelectionKey({dataPoolId, collectionId, publicShareHash});
    return state.selectedIds[selectionKey] ? state.selectedIds[selectionKey].includes(id) : false;
};
export const getAllSelectedIds = (state, {dataPoolId, collectionId, publicShareHash}) => state.selectedIds[getSelectionKey({
        dataPoolId,
        collectionId,
        publicShareHash
    })] || [];
export const getSelectedItemsFetchState = (state) => state.selectedItemsFetchState;

// Sorting
export const getCurrentOrderBy = (state) => state.currentOrderBy;
export const getOrderByOptions = state => (state.orderByOptions);


// view
export const getCurrentView = (state) => getListViewAttributes(state).length
    ? state.view
    : TILE;


// navigation
export const getNavigationType = (state) => state.navigationType;
export const getSelectedFolderPath = state => state.activeFolderPath;

export const getSelectedTagIds = state => (state.activeTagIds || []);

export const isTagSelectedById = (state, id) =>
    state.navigationType === TAGS && getSelectedTagIds(state).includes(id);

// Sidebar list
export const getSidebarActiveId = (state) => state.sidebarActiveId;
export const getAllSidebarListIds = (state) => state.sidebarItemsIds;
export const getSidebarListFetchingState = (state) => state.sidebarItemsFetchState;


// Params
export const getListParams = (state) => {
    let params =  ([
        ...getCurrentFilterParams(state),
    ]);

    let currentPageNumber = listSelectors.getCurrentPageNumber(state);
    if (currentPageNumber && currentPageNumber !== DEFAULT_PAGE) {
        params.push(['page', currentPageNumber]);
    }

    if (getCurrentOrderBy(state)) {
        params.push(['currentOrderBy', encodeURIComponent(getCurrentOrderBy(state))]);
    }

    let selectedFolderPath = getSelectedFolderPath(state);
    if (getNavigationType(state) === FOLDERS && selectedFolderPath && selectedFolderPath !== getConfig('list.folders.root.path')) {
        params.push(['folder', encodeURIComponent(selectedFolderPath)]);
    }

    if (getNavigationType(state) === TAGS && getSelectedTagIds(state).length) {
        params = params.concat(getSelectedTagIds(state).map(id => ['tags[]', encodeURIComponent(id)]));
    }

    return params;
};

export const getListParamNames = (state) => {
    let filterNames = getAllFilters(state)
        .map(filter => {
            if (!filtersByType[filter.type].getSerializeName) {
                throw new Error(`No getSerializeName function for filter type ${filter.type}`)
            }

            return filtersByType[filter.type].getSerializeName(filter);
        });

    return ['page', 'currentOrderBy', 'folder', 'tags[]', ...filterNames];
};


// Misc
export const getListViewAttributes = (state) => state.listViewAttributes;
export const getPermissions = (state) => state.permissions;

export const getUpdateModalState = (state) => state.updateItemModalOpen;
export const getUpdateModalIds = (state) => state.updateItemModalIds;
export const getUpdateItemModalDataPoolId = (state) => state.updateItemModalDataPoolId;
export const getUpdateItemModalLoading = (state) => state.updateItemModalLoading;

//upload folder
export const getUploadFolder = (state) => state.uploadFolder;


export const selectors = {
    ...listSelectors,
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
    getCurrentView,
    getNavigationType,
    getSelectedFolderPath,
    getSelectedTagIds,
    getSelectedItemsFetchState,
    getSidebarListFetchingState,
    getAllSidebarListIds,
    getSidebarActiveId,
    isTagSelectedById,
    getListParams,
    getListParamNames,
    getListViewAttributes,
    getCurrentOrderBy,
    getOrderByOptions,
    getPermissions,
    getUpdateModalState,
    getUpdateModalIds,
    getUpdateItemModalDataPoolId,
    getUpdateItemModalLoading,
    getUploadFolder
};