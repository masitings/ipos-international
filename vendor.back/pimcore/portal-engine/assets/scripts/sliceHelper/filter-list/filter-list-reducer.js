/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {
    arrayToObject,
    deserializeParamsToObject,
    getObjectFromLocalStorage,
    identity, removeDuplicatesFromArray
} from "~portal-engine/scripts/utils/utils";
import {FAILED, FETCHING, NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";

import {initialState as initialListState, createReducer as createListReducer} from "~portal-engine/scripts/sliceHelper/list/list-reducer"
import {FOLDERS, TAGS} from "~portal-engine/scripts/consts/list-navigation-types";
import {getConfig} from "~portal-engine/scripts/utils/general";
import {getAllFilters} from "~portal-engine/scripts/sliceHelper/filter-list/filter-list-selectors";
import * as filtersByType from "~portal-engine/scripts/components/filter/inputs/index";
import {TILE} from "~portal-engine/scripts/consts/teaser-list-views";
import {LIST_SELECTION, TEASER_LIST_VIEW} from "~portal-engine/scripts/consts/local-storage-keys";
import {DEFAULT_PAGE} from "~portal-engine/scripts/consts";
import {REMOVED_FROM_COLLECTION_TYPES} from "~portal-engine/scripts/features/collections/collections-actions";
import {getSelectionKey} from "~portal-engine/scripts/sliceHelper/filter-list/filter-list-utils";

export const initialState = {
    ...initialListState,

    // filter
    filterFetchState: NOT_ASKED,
    filterStatesFetchState: NOT_ASKED,
    filtersByName: {},
    filterStatesByName: {},
    allFilterNames: [],

    // sorting
    currentOrderBy: null,
    orderByOptions: [],

    // navigation
    navigationType: FOLDERS,
    activeFolderPath: null,
    activeTagIds: [],

    // sidebar
    sidebarItemsIds: [],
    sidebarItemsFetchState: NOT_ASKED,

    // misc
    view: getObjectFromLocalStorage(TEASER_LIST_VIEW) || TILE,
    selectedIds: getConfig('selection.disablePersistency')
        ? {}
        : (getObjectFromLocalStorage(LIST_SELECTION) || {}),
    listViewAttributes: [],
    permissions: {},

    updateItemModalOpen: false
};

let folderConfig = getConfig('list.folders');
if (folderConfig && folderConfig.active && folderConfig.root) {
    initialState.activeFolderPath = folderConfig.root.path;
    initialState.navigationType = FOLDERS;
}


export function createReducer({ACTION_TYPES, payloadMapper = identity}) {
    const listReducer = createListReducer({ACTION_TYPES, payloadMapper});

    return {
        // list
        ...listReducer,
        [ACTION_TYPES.PAGE_FETCH_SUCCEEDED]: (state, action) => {
            const {payload: {data: {currentOrderBy, orderByOptions, listViewAttributes, currentFolder}}} = action;
            listReducer[ACTION_TYPES.PAGE_FETCH_SUCCEEDED](state, action);

            state.currentOrderBy = currentOrderBy;
            state.orderByOptions = orderByOptions;
            state.listViewAttributes = listViewAttributes;

            if (currentFolder) {
                state.permissions = currentFolder.permissions;
            }
        },
        [ACTION_TYPES.URL_CHANGED]: (state, action) => {
            const {payload: {params}} = action;

            let {currentOrderBy, folder, tags} = deserializeParamsToObject(params);
            listReducer[ACTION_TYPES.URL_CHANGED](state, action);

            if (currentOrderBy) {
                state.currentOrderBy = currentOrderBy;
            }

            if (tags) {
                state.activeTagIds = tags.map(Number);
                state.navigationType = TAGS;
            } else {
                state.navigationType = FOLDERS;

                if (folder) {
                    state.activeFolderPath = folder;
                } else {
                    // no params means root path
                    state.activeFolderPath = getConfig('list.folders.root.path');
                }
            }

            getAllFilters(state).forEach(filter => {
                let type = filter.type;

                let deserializeFunction = filtersByType[type].deserialize;
                if (!deserializeFunction) {
                    throw new Error(`Missing deserialize function for filter type ${type}`)
                }

                state.filterStatesByName[filter.name] = {
                    ...state.filterStatesByName[filter.name],
                    ...deserializeFunction(filter, params)
                };
            });

            clearPages(state);
            state.filterStatesFetchState = NOT_ASKED;
        },

        // filter structure
        [ACTION_TYPES.FILTER_STRUCTURE_REQUESTED]: function (state) {
            state.filterFetchState = FETCHING;
            state.filterStatesFetchState = FETCHING;
        },
        [ACTION_TYPES.FILTER_STRUCTURE_SUCCEEDED]: function (state, {payload: {data: {filters = [], filtersData}}}) {
            state.filterFetchState = SUCCESS;
            state.filterStatesFetchState = SUCCESS;

            state.filtersByName = arrayToObject(filters, 'name');
            state.filterStatesByName = filtersData;
            state.allFilterNames = filters.map(filter => filter.name);
        },
        [ACTION_TYPES.FILTER_STRUCTURE_FAILED]: function (state) {
            state.filterFetchState = FAILED;
            state.filterStatesFetchState = FAILED;
        },

        // filter state
        [ACTION_TYPES.FILTER_STATES_REQUESTED]: function (state) {
            state.filterStatesFetchState = FETCHING;
        },
        [ACTION_TYPES.FILTER_STATES_SUCCEEDED]: function (state, {payload: {data: {filtersData}}}) {
            state.filterStatesFetchState = SUCCESS;
            state.filterStatesByName = filtersData;
        },
        [ACTION_TYPES.FILTER_STATES_FAILED]: function (state) {
            state.filterStatesFetchState = FAILED;
        },
        [ACTION_TYPES.FILTER_CHANGED]: function (state, {payload}) {
            let currentFilter = state.filtersByName[payload.name];
            let currentFilterState = state.filterStatesByName[payload.name];
            let type = currentFilter.type;

            let updateFunction = filtersByType[type].update;
            if (!updateFunction) {
                console.error(`Missing update function for filter type ${type}`);
                return state;
            }

            state.filterStatesByName[payload.name] = {
                ...currentFilterState,
                ...updateFunction(currentFilterState, payload)
            };

            clearPages(state);
            state.filterStatesFetchState = NOT_ASKED;
            state.currentPage = DEFAULT_PAGE;
        },
        [ACTION_TYPES.ALL_FILTERS_CLEARED]: function (state, {payload}) {
            state.allFilterNames.forEach(filterName => {
                let currentFilter = state.filtersByName[filterName];
                let currentFilterState = state.filterStatesByName[filterName];
                let type = currentFilter.type;

                let clearAllFunction = filtersByType[type].clearAll;
                if (!clearAllFunction) {
                    console.error(`Missing clear all function for filter type ${type}`);
                    return;
                }

                state.filterStatesByName[filterName] = {
                    ...currentFilterState,
                    ...clearAllFunction(currentFilterState, payload)
                };
            });

            state.activeTagIds = [];

            let rootFolderPath = getConfig('list.folders.root.path');
            if (rootFolderPath) {
                state.activeFolderPath = rootFolderPath;
                state.navigationType = FOLDERS;
            }

            clearPages(state);
            state.filterStatesFetchState = NOT_ASKED;
            state.currentPage = DEFAULT_PAGE;
        },
        [ACTION_TYPES.FILTER_CLEARED]: function (state, {payload}) {
            let currentFilter = state.filtersByName[payload.name];
            let currentFilterState = state.filterStatesByName[payload.name];
            let type = currentFilter.type;

            let clearFunction = filtersByType[type].clear;
            if (!clearFunction) {
                console.error(`Missing clear function for filter type ${type}`);
                return state;
            }

            state.filterStatesByName[payload.name] = {
                ...currentFilterState,
                ...clearFunction(currentFilterState, payload)
            };

            clearPages(state);
            state.filterStatesFetchState = NOT_ASKED;
            state.currentPage = DEFAULT_PAGE;
        },

        // navigation filter
        [ACTION_TYPES.NAVIGATION_CHANGED]: function (state, {payload}) {
            state.navigationType = payload;
            clearPages(state);
        },
        [ACTION_TYPES.SELECTED_FOLDER]: function (state, {payload: {path}}) {
            state.activeFolderPath = path;

            clearPages(state);
            state.currentPage = DEFAULT_PAGE;
        },
        [ACTION_TYPES.TOGGLED_TAG_STATE]: function (state, {payload: {id, state: tagState}}) {
            state.activeTagIds = tagState
                ? [...state.activeTagIds, id]
                : state.activeTagIds.filter(currentId => currentId !== id);

            clearPages(state);
            state.currentPage = DEFAULT_PAGE;
        },

        // sorting
        [ACTION_TYPES.ORDER_BY_CHANGED]: function (state, {payload: {orderBy}}) {
            state.currentOrderBy = orderBy;
            clearPages(state);
            state.currentPage = DEFAULT_PAGE;
        },

        // selection
        [ACTION_TYPES.SELECTION_TOGGLED]: function (state, {payload: {id, isSelected, dataPoolId, collectionId, publicShareHash}}) {
            let selectionKey = getSelectionKey({dataPoolId, collectionId, publicShareHash});
            if (isSelected) {
                addIdsToSelection({state, selectionKey, ids: [id]});
            } else {
                state.selectedIds[selectionKey] = state.selectedIds[selectionKey].filter(currentId => currentId !== id);
            }
        },
        [ACTION_TYPES.SELECTION_TOGGLED_ALL]: function (state, {payload: {ids, isSelected, dataPoolId, collectionId, publicShareHash}}) {
            let selectionKey = getSelectionKey({dataPoolId, collectionId, publicShareHash});
            if (isSelected) {
                // state.selectedIds.push(id);
            } else {
                state.selectedIds[selectionKey] = ids.forEach(
                    id => state.selectedIds[selectionKey].filter(
                        currentId => currentId !== id
                    )
                ) || []
            }
        },
        [ACTION_TYPES.SELECTED_ALL_REQUESTED]: function (state, {payload: {dataPoolId, collectionId, publicShareHash}}) {
            // optimistic update with all ids from current page
            let selectionKey = getSelectionKey({dataPoolId, collectionId, publicShareHash});
            let newIds = state.idsByPages[state.currentPage].map(Number);

            addIdsToSelection({state, selectionKey, ids: newIds});
        },
        [ACTION_TYPES.SELECTED_ALL_SUCCEEDED]: function (state, {payload: {data: newIds, dataPoolId, collectionId, publicShareHash}}) {
            let selectionKey = getSelectionKey({dataPoolId, collectionId, publicShareHash});

            addIdsToSelection({state, selectionKey, ids: newIds});
        },
        [ACTION_TYPES.SELECTED_ALL_FAILED]: function () {

        },

        [ACTION_TYPES.SELECTED_ITEMS_REQUESTED]: function (state) {
            state.selectedItemsFetchState = FETCHING;
        },
        [ACTION_TYPES.SELECTED_ITEMS_SUCCEEDED]: function (state, {payload: {data}}) {
            state.selectedItemsFetchState = SUCCESS;
            state.itemsById = {
                ...state.itemsById,
                ...arrayToObject(data.items),
            }
        },
        [ACTION_TYPES.SELECTED_ITEMS_FAILED]: function (state) {
            state.selectedItemsFetchState = FAILED;
        },

        [ACTION_TYPES.SIDEBAR_ITEMS_REQUESTED]: function (state) {
            state.sidebarItemsFetchState = FETCHING;
        },
        [ACTION_TYPES.SIDEBAR_ITEMS_SUCCEEDED]: function (state, {payload: {data}}) {
            state.sidebarItemsFetchState = SUCCESS;
            state.sidebarActiveId = data.activeItem;
            state.sidebarItemsIds = data.items.map((item) => item.id);
            state.itemsById = {
                ...state.itemsById,
                ...arrayToObject(data.items),
            }
        },
        [ACTION_TYPES.SIDEBAR_ITEMS_FAILED]: function (state) {
            state.sidebarItemsFetchState = FAILED;
        },

        // Misc
        [ACTION_TYPES.LIST_ITEM_VIEW]: function (state, {payload: {view}}) { // todo rename
            if (view) {
                state.view = view;
            }
        },
        [ACTION_TYPES.RELOCATE_ITEMS_CLICKED]: function (state, {payload: {dataPoolId, ids}}) {
            state.updateItemModalOpen = true;
            state.updateItemModalIds = ids;
            state.updateItemModalDataPoolId = dataPoolId;
        },
        [ACTION_TYPES.RELOCATE_MODAL_CLOSED]: function (state) {
            state.updateItemModalOpen = false;
        },
        [ACTION_TYPES.RELOCATE_ITEMS_REQUESTED]: function (state, {payload: {ids}}) {
            state.updateItemModalLoading = true;
        },
        [ACTION_TYPES.RELOCATE_ITEMS_SUCCEEDED]: function (state, {payload: {ids, batchTask}}) {
            state.updateItemModalOpen = false;

            //todo
            if (ids.length === 1 || batchTask === false) {
                clearPages(state);
            }

            state.updateItemModalLoading = false;
        },
        [REMOVED_FROM_COLLECTION_TYPES.SUCCEEDED]: function (state, {payload: {dataPoolId, collectionId, ids}}) {
            if (state.id === dataPoolId && state.collectionId === collectionId) {
                let selectionKey = getSelectionKey({dataPoolId, collectionId});
                state.selectedIds[selectionKey] = ids.forEach(id => (state.selectedIds[selectionKey] || []).filter(currentId => currentId !== id)) || [];
                clearPages(state);
            }
        },
        [ACTION_TYPES.DELETE_ITEMS_SUCCEEDED]: function (state, {payload: {ids, dataPoolId, collectionId, batchTask}}) {
            let selectionKey = getSelectionKey({dataPoolId, collectionId});
            state.selectedIds[selectionKey] = ids.forEach(id => (state.selectedIds[selectionKey] || []).filter(currentId => currentId !== id)) || [];

            //todo
            if (ids.length === 1 || batchTask === false) {
                clearPages(state);
            }
        }
    };
}

function addIdsToSelection({state, selectionKey, ids}) {
    if (!state.selectedIds[selectionKey]) {
        state.selectedIds[selectionKey] = [];
    }

    state.selectedIds[selectionKey] = removeDuplicatesFromArray([
        ...state.selectedIds[selectionKey],
        ...ids
    ])
}

function clearPages(state) {
    state.idsByPages = {};
    state.fetchingStateByPage = {};
    state.fetchingMessageByPage = {};
}