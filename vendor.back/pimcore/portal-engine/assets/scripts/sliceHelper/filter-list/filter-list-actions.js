/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {createActionCreators as createListActionCreators} from "~portal-engine/scripts/sliceHelper/list/list-actions";
import {createFetchActions} from "~portal-engine/scripts/utils/fetch";
import {LIST_SELECTION, TEASER_LIST_VIEW} from "~portal-engine/scripts/consts/local-storage-keys";
import {createAction} from "@reduxjs/toolkit";
import {
    getObjectFromLocalStorage,
    removeDuplicatesFromArray,
    setObjectToLocalStorage
} from "~portal-engine/scripts/utils/utils";
import {getConfig} from "~portal-engine/scripts/utils/general";
import {getSelectionKey} from "~portal-engine/scripts/sliceHelper/filter-list/filter-list-utils";
import {fetchTasks} from "~portal-engine/scripts/features/tasks/tasks-actions";
import {showNotification} from "~portal-engine/scripts/features/notifications/notifications-actions";
import * as NOTIFICATION_TYPES from "~portal-engine/scripts/consts/notification-types";

export function createActionCreators({actionTypePrefix, api, selectors}) {
    const {
        ACTION_TYPES: LIST_TYPES,
        actionCreators: listActionCreators,
    } = createListActionCreators({
        actionTypePrefix: actionTypePrefix,
        api,
        selectors: selectors
    });


    // list
    const setup = function (params) {
        return function (dispatch) {
            dispatch(requestFilterStructure(params));
            dispatch(listActionCreators.setup(params));
        }
    };

    // filter structure
    const {
        actionTypes: FILTER_STRUCTURE_FETCHING_TYPES,
        actionCreator: requestFilterStructure,
    } = createFetchActions(
        `${actionTypePrefix}/filter/structure`,
        (state, {params}) => api.fetchFilterStructure(params).response,
        (state, payload) => ({
            params: payload
        })
    );

    // filter states fetching
    const {
        actionTypes: FILTER_STATES_FETCHING_TYPES,
        actionCreator: requestCurrentFilterState,
    } = createFetchActions(
        `${actionTypePrefix}/filter/states`,
        (state, payload) => api.fetchFilterStates(payload.params).response,
        (state) => ({
            params: selectors.getListParams(state)
        })
    );


    // filter
    const FILTER_CHANGED = `${actionTypePrefix}/filter/changed`;
    const changeFilter = payload => ({
        type: FILTER_CHANGED,
        payload
    });

    const ALL_FILTERS_CLEARED = `${actionTypePrefix}/filter/cleared-all`;
    const clearAllFilters = () => ({
        type: ALL_FILTERS_CLEARED
    });

    const FILTER_CLEARED = `${actionTypePrefix}/filter/cleared-single`;
    const clearFilter = payload => ({
        type: FILTER_CLEARED,
        payload
    });


    // sorting
    const ORDER_BY_CHANGED = `${actionTypePrefix}/order/changed`;
    const setCurrentOrderBy = orderBy => ({
        type: ORDER_BY_CHANGED,
        payload: {
            orderBy
        }
    });

    // selection
    const SELECTION_TOGGLED = `${actionTypePrefix}/selection/toggled-single`;
    const toggleSelection = ({id, isSelected, dataPoolId, collectionId, publicShareHash}) => {
        let selectionKey = getSelectionKey({dataPoolId, collectionId, publicShareHash});
        if (!getConfig('selection.disablePersistency')) {
            let selectedIdsByDataPoolId = getObjectFromLocalStorage(LIST_SELECTION) || {};

            if (isSelected) {
                if (!selectedIdsByDataPoolId[selectionKey]) {
                    selectedIdsByDataPoolId[selectionKey] = [];
                }
                selectedIdsByDataPoolId[selectionKey].push(id);
            } else {
                selectedIdsByDataPoolId[selectionKey] = selectedIdsByDataPoolId[selectionKey].filter(currentId => currentId !== id);
            }

            setObjectToLocalStorage(LIST_SELECTION, selectedIdsByDataPoolId);
        }

        return ({
            type: SELECTION_TOGGLED,
            payload: {
                id,
                isSelected,
                dataPoolId,
                collectionId,
                publicShareHash
            }
        })
    };


    const SELECTION_TOGGLED_ALL = `${actionTypePrefix}/selection/toggled-all`;
    const toggleSelectionAll = ({ids, isSelected, dataPoolId, collectionId, publicShareHash}) => {
        let selectedIdsByDataPoolId = getObjectFromLocalStorage(LIST_SELECTION) || {};
        let selectionKey = getSelectionKey({dataPoolId, collectionId, publicShareHash});
        
        if (isSelected) {
            // use case not defined
        } else {
            selectedIdsByDataPoolId[selectionKey] = ids.forEach(id =>
                (selectedIdsByDataPoolId[selectionKey] || []).filter(currentId => currentId !== id)) || [];
        }

        setObjectToLocalStorage(LIST_SELECTION, selectedIdsByDataPoolId);

        return ({
            type: SELECTION_TOGGLED_ALL,
            payload: {
                ids: ids,
                isSelected,
                dataPoolId,
                collectionId,
                publicShareHash
            }
        })
    };

    const {
        actionTypes: SELECTED_ALL,
        actionCreator: selectAll,
    } = createFetchActions(
        `${actionTypePrefix}/selected-all`,
        (state, {dataPoolId, collectionId, publicShareHash}) => {
            let request = api.fetchAllSelectableIds(selectors.getListParams(state)).response;

            request.then(function ({success, data: ids}) {
                if (success) {
                    let selectionKey = getSelectionKey({dataPoolId, collectionId, publicShareHash});
                    let selectedIdsByDataPoolId = getObjectFromLocalStorage(LIST_SELECTION) || {};

                    selectedIdsByDataPoolId[selectionKey] = removeDuplicatesFromArray(
                        [
                            ...(selectedIdsByDataPoolId[selectionKey] || []),
                            ...ids
                        ]
                    );

                    setObjectToLocalStorage(LIST_SELECTION, selectedIdsByDataPoolId);
                }
            });

            return request;
        },
    );

    const {
        actionTypes: SELECTED_ITEMS_TYPE,
        actionCreator: requestSelectedItems,
    } = createFetchActions(
        `${actionTypePrefix}/selected-items`,
        (state, {params}) => api.getSelectedItems(params).response,
        (state, payload) => ({
            params: payload
        })
    );

    const {
        actionTypes: SIDEBAR_ITEMS_TYPE,
        actionCreator: requestSidebarItems,
    } = createFetchActions(
        `${actionTypePrefix}/sidebar-items`,
        (state) => api.getSidebarItems().response,
        (state, payload) => ({
            params: payload
        })
    );



    // view
    const LIST_ITEM_VIEW = `${actionTypePrefix}/item-view/changed`;
    const changeItemView = (view) => {
        setObjectToLocalStorage(TEASER_LIST_VIEW, view);

        return {
            type: LIST_ITEM_VIEW,
            payload: {
                view: view
            }
        }
    };

    // delete item
    const {
        actionTypes: DELETE_ITEMS_TYPE,
        actionCreator: deleteClicked,
    } = createFetchActions(
        `${actionTypePrefix}/delete-items`,
        (state, params, dispatch) => {
            let deleteRequest;

            if (params.ids.length > 1) {
                deleteRequest = api.deleteMultiItems(params).response;
            } else {
                deleteRequest = api.deleteItem(params).response;
            }

            deleteRequest.then((response) => {
                let dataPoolId = params.dataPoolId;
                let collectionId = params.collectionId;

                let selectedIdsByDataPoolId = getObjectFromLocalStorage(LIST_SELECTION) || {};
                let selectionKey = getSelectionKey({dataPoolId, collectionId});

                selectedIdsByDataPoolId[selectionKey] = params.ids.forEach(id =>
                    (selectedIdsByDataPoolId[selectionKey] || []).filter(currentId => currentId !== id)) || [];

                setObjectToLocalStorage(LIST_SELECTION, selectedIdsByDataPoolId);

                if (response.batchTask === true) {
                    dispatch(fetchTasks())
                } else {
                    showNotification({
                        type: NOTIFICATION_TYPES.SUCCESS,
                        translation: "asset.deleted"
                    });
                }
            });

            return deleteRequest;
        }
    );
    // update item
    const RELOCATE_ITEMS_CLICKED = `${actionTypePrefix}/relocate-items/modal-open`;
    const relocateItemClicked = ({dataPoolId, ids}) => {
        return {
            type: RELOCATE_ITEMS_CLICKED,
            payload: {
                dataPoolId: dataPoolId,
                ids: ids
            }
        }
    };

    const RELOCATE_MODAL_CLOSED = `${actionTypePrefix}/relocate-items/modal-closed`;
    const closeRelocateModal = createAction(RELOCATE_MODAL_CLOSED);

    const {
        actionTypes: RELOCATE_ITEMS_TYPE,
        actionCreator: relocateItems,
    } = createFetchActions(
        `${actionTypePrefix}/relocate-items`,
        (state, params, dispatch) => {
            let relocateRequest;

            if (params.ids.length > 1) {
                relocateRequest = api.relocateMultiItems(params).response;
            } else {
                relocateRequest = api.relocateItem(params).response;
            }

            relocateRequest.then((response) => {
                if (response.batchTask === true) {
                    dispatch(fetchTasks())
                } else {
                    showNotification({
                        type: NOTIFICATION_TYPES.SUCCESS,
                        translation: "relocate.item"
                    });
                }
            });

            return relocateRequest;
        }
    );

    // navigation
    const NAVIGATION_CHANGED = `${actionTypePrefix}/navigation/changed`;
    const navigationTypeChanged = createAction(NAVIGATION_CHANGED);
    const SELECTED_FOLDER = `${actionTypePrefix}/navigation/folder/selected`;
    const selectFolder = createAction(SELECTED_FOLDER);
    const TOGGLED_TAG_STATE = `${actionTypePrefix}/navigation/tag/toggled`;
    const toggleTagSelection = createAction(TOGGLED_TAG_STATE);


    const ACTION_TYPES = {
        ...LIST_TYPES,
        FILTER_STRUCTURE_REQUESTED: FILTER_STRUCTURE_FETCHING_TYPES.REQUESTED,
        FILTER_STRUCTURE_SUCCEEDED: FILTER_STRUCTURE_FETCHING_TYPES.SUCCEEDED,
        FILTER_STRUCTURE_FAILED: FILTER_STRUCTURE_FETCHING_TYPES.FAILED,
        FILTER_STATES_REQUESTED: FILTER_STATES_FETCHING_TYPES.REQUESTED,
        FILTER_STATES_SUCCEEDED: FILTER_STATES_FETCHING_TYPES.SUCCEEDED,
        FILTER_STATES_FAILED: FILTER_STATES_FETCHING_TYPES.FAILED,
        SELECTED_ITEMS_REQUESTED: SELECTED_ITEMS_TYPE.REQUESTED,
        SELECTED_ITEMS_SUCCEEDED: SELECTED_ITEMS_TYPE.SUCCEEDED,
        SELECTED_ITEMS_FAILED: SELECTED_ITEMS_TYPE.FAILED,
        SIDEBAR_ITEMS_REQUESTED: SIDEBAR_ITEMS_TYPE.REQUESTED,
        SIDEBAR_ITEMS_SUCCEEDED: SIDEBAR_ITEMS_TYPE.SUCCEEDED,
        SIDEBAR_ITEMS_FAILED: SIDEBAR_ITEMS_TYPE.FAILED,
        FILTER_CHANGED,
        FILTER_CLEARED,
        ALL_FILTERS_CLEARED,
        SELECTION_TOGGLED,
        SELECTION_TOGGLED_ALL,
        SELECTED_ALL_REQUESTED: SELECTED_ALL.REQUESTED,
        SELECTED_ALL_SUCCEEDED: SELECTED_ALL.SUCCEEDED,
        SELECTED_ALL_FAILED: SELECTED_ALL.FAILED,
        ORDER_BY_CHANGED,
        LIST_ITEM_VIEW,
        NAVIGATION_CHANGED,
        SELECTED_FOLDER,
        TOGGLED_TAG_STATE,
        RELOCATE_ITEMS_CLICKED,
        RELOCATE_MODAL_CLOSED,
        RELOCATE_ITEMS_REQUESTED: RELOCATE_ITEMS_TYPE.REQUESTED,
        RELOCATE_ITEMS_SUCCEEDED: RELOCATE_ITEMS_TYPE.SUCCEEDED,
        RELOCATE_ITEMS_FAILED: RELOCATE_ITEMS_TYPE.FAILED,
        DELETE_ITEMS_REQUESTED: DELETE_ITEMS_TYPE.REQUESTED,
        DELETE_ITEMS_SUCCEEDED: DELETE_ITEMS_TYPE.SUCCEEDED,
        DELETE_ITEMS_FAILED: DELETE_ITEMS_TYPE.FAILED,
    };

    return {
        ACTION_TYPES,
        actionCreators: {
            ...listActionCreators,
            setup,
            requestCurrentFilterState,
            changeFilter,
            clearAllFilters,
            clearFilter,
            setCurrentOrderBy,
            toggleSelection,
            toggleSelectionAll,
            selectAll,
            requestSelectedItems,
            requestSidebarItems,
            changeItemView,
            navigationTypeChanged,
            selectFolder,
            toggleTagSelection,
            relocateItemClicked,
            closeRelocateModal,
            relocateItems,
            deleteClicked,
        }
    }
}

export default createActionCreators;