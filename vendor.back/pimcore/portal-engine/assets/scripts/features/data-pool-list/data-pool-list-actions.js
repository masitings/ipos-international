/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import * as api from "~portal-engine/scripts/features/data-pool-list/data-pool-list-api";
import {createActionCreators} from "~portal-engine/scripts/sliceHelper/filter-list/filter-list-actions";
import {dataPoolSelectors} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";

const {ACTION_TYPES, actionCreators} = createActionCreators({
    actionTypePrefix: 'dataPoolList',
    api,
    selectors: dataPoolSelectors
});

export const DATA_POOL_LIST_ACTION_TYPES = ACTION_TYPES;
export const dataPoolListActionCreators = actionCreators;

export const {
    setup,
    changePage,
    urlChanged,
    requestListPage,
    requestCurrentFilterState,
    changeFilter,
    clearAllFilters,
    clearFilter,
    toggleSelection,
    toggleSelectionAll,
    selectAll,
    setCurrentOrderBy,
    changeItemView,
    navigationTypeChanged,
    selectFolder,
    toggleTagSelection,
    requestSelectedItems,
    requestSidebarItems,
    relocateItemClicked,
    closeRelocateModal,
    relocateItems,
    triggerRelocateItems,
    deleteClicked
} = actionCreators;

export const {
    SETUP,
    PAGE_REQUESTED,
    PAGE_FETCH_SUCCEEDED,
    PAGE_FETCH_FAILED,
    PAGE_CHANGED,
    URL_CHANGED,
    FILTER_STRUCTURE_REQUESTED,
    FILTER_STRUCTURE_SUCCEEDED,
    FILTER_STRUCTURE_FAILED,
    FILTER_STATES_REQUESTED,
    FILTER_STATES_SUCCEEDED,
    FILTER_STATES_FAILED,
    FILTER_CHANGED,
    FILTER_CLEARED,
    ALL_FILTERS_CLEARED,
    SELECTION_TOGGLED,
    SELECTION_TOGGLED_ALL,
    ORDER_BY_CHANGED,
    LIST_ITEM_VIEW,
    NAVIGATION_CHANGED,
    SELECTED_FOLDER,
    TOGGLED_TAG_STATE,
    RELOCATE_ITEMS_CLICKED,
    RELOCATE_MODAL_CLOSED,
    RELOCATE_ITEMS_REQUESTED,
    RELOCATE_ITEMS_SUCCEEDED,
    RELOCATE_ITEMS_FAILED,
    TRIGGER_RELOCATE_REQUESTED,
    TRIGGER_RELOCATE_SUCCEEDED,
    TRIGGER_RELOCATE_FAILED,
    DELETE_ITEMS_REQUESTED,
    DELETE_ITEMS_SUCCEEDED,
    DELETE_ITEMS_FAILED
} = ACTION_TYPES;