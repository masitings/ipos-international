/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {createAction} from "@reduxjs/toolkit";
import {createFetchActions} from "~portal-engine/scripts/utils/fetch";
import * as api from "~portal-engine/scripts/features/collections/collections-api";
import {trans} from "~portal-engine/scripts/utils/intl";
import React from "react";
import {createActionCreators} from "~portal-engine/scripts/sliceHelper/list/list-actions";
import * as collectionSelectors from "~portal-engine/scripts/features/collections/collections-selectors";
import {getCollectionList} from "~portal-engine/scripts/features/collections/collections-api";
import {showNotification} from "~portal-engine/scripts/features/notifications/notifications-actions";
import * as NOTIFICATION_TYPES from "~portal-engine/scripts/consts/notification-types";
import {getObjectFromLocalStorage, setObjectToLocalStorage} from "~portal-engine/scripts/utils/utils";
import {LIST_SELECTION} from "~portal-engine/scripts/consts/local-storage-keys";
import {getSelectionKey} from "~portal-engine/scripts/sliceHelper/filter-list/filter-list-utils";

export const addToCollectionClicked = createAction('collections/add-to/clicked', ({dataPoolId, ids}) => ({
    payload: {
        dataPoolId,
        ids
    }
}));

export const closeAddToModal = createAction('collections/add-to/modal-closed');

export const {
    actionTypes: ADDED_TO_COLLECTION_TYPES,
    actionCreator: addedToCollection
} = createFetchActions(
    'collections/add-to',
    (state, {dataPoolId, selectedIds, collectionId}) => {
        let request = api.addToCollection({dataPoolId, ids: selectedIds, collectionId});
        let translationRequest = trans('collection.add-to-collection');

        Promise.all([request, translationRequest]).then(([{success, data: {name, detailUrl}}, translation]) => {
            if (success) {
                showNotification({
                    type: NOTIFICATION_TYPES.SUCCESS,
                    message: translation.replace('[name]', name),
                    action: detailUrl,
                    actionLabelTranslated: "collection.open-collection"
                });
            }
        });

        return request;
    }
);

export const {
    actionTypes: ADDED_TO_NEW_COLLECTION_TYPES,
    actionCreator: addedToNewCollection
} = createFetchActions(
    'collections/add-to-new',
    (state, {dataPoolId, selectedIds, collectionName}) => {
        let request = api.createCollection({name: collectionName})
            .then(({data: {id}}) => api.addToCollection({ids: selectedIds, dataPoolId, collectionId: id}));
        let translationRequest = trans('collection.add-to-new-collection');

        Promise.all([request, translationRequest]).then(([{success, data: {name, detailUrl}}, translation]) => {
            if (success) {
                showNotification({
                    type: NOTIFICATION_TYPES.SUCCESS,
                    message: translation.replace('[name]', collectionName),
                    action: detailUrl,
                    actionLabelTranslated: "collection.open-collection"
                });
            }
        });

        return request;
    }
);

export const {
    actionTypes: REMOVED_FROM_COLLECTION_TYPES,
    actionCreator: removeFromCollection
} = createFetchActions(
    'collections/removed-from',
    (state, {dataPoolId, ids, collectionId}) => {
        let request = api.removeFromCollection({dataPoolId, ids, collectionId});

        request.then(({success}) => {
            if (success) {
                let selectedIdsByDataPoolId = getObjectFromLocalStorage(LIST_SELECTION) || {};
                let selectionKey = getSelectionKey({dataPoolId, collectionId});

                selectedIdsByDataPoolId[selectionKey] = ids.forEach(id =>
                    (selectedIdsByDataPoolId[selectionKey] || []).filter(currentId => currentId !== id)) || [];

                setObjectToLocalStorage(LIST_SELECTION, selectedIdsByDataPoolId);

                showNotification({
                    type: NOTIFICATION_TYPES.SUCCESS,
                    translation: 'collection.removed-from-collection'
                });
            }
        });

        return request;
    }
);

// Collection list
export const {
    ACTION_TYPES: COLLECTION_LIST_TYPES,
    actionCreators: listActionCreators,
} = createActionCreators({
    actionTypePrefix: 'collections/list',
    api: {fetchList: getCollectionList},
    selectors: collectionSelectors
});

export const {
    urlChanged,
    setup,
    changePage,
    requestListPage
} = listActionCreators;


export const {
    actionTypes: DELETED_COLLECTION_TYPES,
    actionCreator: deleteCollection
} = createFetchActions(
    'collections/deleted',
    (state, {id}) => {
        let request = api.deleteCollection({id});

        request.then(({success}) => {
            if (success) {
                showNotification({
                    type: NOTIFICATION_TYPES.SUCCESS,
                    translation: "collection.deleted"
                });
            }
        });

        return request;
    }
);

export const {
    actionTypes: RENAMED_COLLECTION_TYPES,
    actionCreator: renameCollection
} = createFetchActions(
    'collections/renamed',
    (state, {id, name}) => {
        let request = api.renameCollection({id, name});

        request.then(({success}) => {
            if (success) {
                showNotification({
                    type: NOTIFICATION_TYPES.SUCCESS,
                    translation: "collection.renamed"
                });
            }
        });

        return request;
    }
);

export const {
    actionTypes: CREATED_COLLECTION_TYPES,
    actionCreator: createCollection
} = createFetchActions(
    'collections/created',
    (state, {name}) => {
        let request = api.createCollection({name});
        let translationRequest = trans('collection.created');

        Promise.all([request, translationRequest]).then(([{success}, translation]) => {
            if (success) {
                showNotification({
                    type: NOTIFICATION_TYPES.SUCCESS,
                    message: translation.replace('[name]', name)
                });
            }
        });

        return request;
    });


// sharing
export const {
    actionTypes: COLLECTION_SHARE_LIST_REQUEST_TYPES,
    actionCreator: requestCollectionShareList
} = createFetchActions(
    'collections/share-list',
    (state, {collectionId}) => api.getShareList({collectionId}).response);

export const {
    actionTypes: UPDATE_COLLECTION_SHARE_LIST_TYPES,
    actionCreator: updateCollectionShareList
} = createFetchActions(
    'collections/share-list/changed',
    (state, {collectionId, permissions}) => {
        let request = api.updateCollectionShareList({collectionId, permissions});

        request.then(({success}) => {
            if (success) {
                showNotification({
                    type: NOTIFICATION_TYPES.SUCCESS,
                    translation: "collection.permission.updated"
                });
            }
        });

        return request;
    });

// detail actions
export const {
    actionTypes: COLLECTION_DETAIL_ACTIONS_TYPES,
    actionCreator: fetchCollectionDetailActions
} = createFetchActions(
    'collections/detail-actions',
    (state, {collectionId}) => api.fetchDetailActions({collectionId}));
