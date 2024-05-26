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
import {showNotification} from "~portal-engine/scripts/features/notifications/notifications-actions";
import * as NOTIFICATION_TYPES from "~portal-engine/scripts/consts/notification-types";
import {showError} from "~portal-engine/scripts/utils/general";
import {createActionCreators} from "~portal-engine/scripts/sliceHelper/list/list-actions";
import * as publicShareSelectors from "~portal-engine/scripts/features/public-share/public-share-selectors";
import * as api from "~portal-engine/scripts/features/public-share/public-share-api";
import {getPublicShareList} from "~portal-engine/scripts/features/public-share/public-share-api";
import {downloadSelectionStateToConfig} from "~portal-engine/scripts/features/download/download-utils";
import {mapObject} from "~portal-engine/scripts/utils/utils";
import {getItemById} from "~portal-engine/scripts/features/collections/collections-selectors";

export const publicShareClicked = createAction(
    'public-share/clicked',
    ({ids, dataPool}) => ({
        payload: {
            ids,
            dataPool,
        }
    })
);

export const PUBLIC_SHARE_COLLECTION_CLICKED = 'public-share/share-collection-clicked';

export const publicShareCollectionClicked = ({collectionId, dataPools}) => ({
    type: PUBLIC_SHARE_COLLECTION_CLICKED,
    payload: {
        collectionId,
        dataPools,
    }
});

export const publicShareCollectionListItemClicked = ({collectionId}) =>
    (dispatch, getState) => {
        dispatch(
            publicShareCollectionClicked({
                collectionId: collectionId,
                dataPools: getItemById(getState(), collectionId).dataPools
            })
        );
    };

export const publicShareEditClicked = createAction(
    'public-share/edit-clicked',
    (id) => ({
        payload: {
            id,
        }
    })
);

export const closedPublicShareModal = createAction('public-share/closed');

export const {
    actionTypes: GUEST_SHARING_SUBMITTING_TYPES,
    actionCreator: publicShare
} = createFetchActions(
    'public-share/submit',
    (state, {
        name,
        expiryDate,
        showTermsText,
        termsText,
        dataPoolConfigId,
        itemIds,
        collectionId,
        downloadAttributeSelectionByDataPoolId,
    }) =>
        api.createPublicShare({
            name,
            downloadConfigs: mapObject(downloadAttributeSelectionByDataPoolId, (dataPoolId, selection) => (
                downloadSelectionStateToConfig({...selection, dataPoolId, state})
            )),
            expiryDate,
            showTermsText,
            termsText,
            ...(collectionId
                    ? {collectionId}
                    : {
                        dataPoolConfigId,
                        itemIds,
                    }
            ),
        })
);

export const {
    actionTypes: GUEST_SHARING_UPDATED_TYPES,
    actionCreator: publicShareEdit
} = createFetchActions(
    'public-share/updated',
    (state, {
        id,
        name,
        expiryDate,
        showTermsText,
        termsText,
        downloadAttributeSelectionByDataPoolId,
    }) => {
        let request = api.updatePublicShare({
            id,
            name,
            downloadConfigs: mapObject(downloadAttributeSelectionByDataPoolId, (dataPoolId, selection) => (
                downloadSelectionStateToConfig({...selection, dataPoolId, state})
            )),
            expiryDate,
            showTermsText,
            termsText,
        });

        request.then(function () {
            showNotification({
                type: NOTIFICATION_TYPES.SUCCESS,
                translation: "public-share.updated",
            });
        }).catch(function (error) {
            showError(error)
        });

        return request;
    }
);

/* share list */
export const {
    ACTION_TYPES: PUBLIC_SHARE_LIST_TYPES,
    actionCreators: listActionCreators,
} = createActionCreators({
    actionTypePrefix: 'public-share',
    api: {fetchList: getPublicShareList},
    selectors: publicShareSelectors
});

export const {
    urlChanged,
    setup,
    changePage,
    requestListPage
} = listActionCreators;

export const {
    actionTypes: DELETED_PUBLIC_SHARE_TYPES,
    actionCreator: deletePublicShare
} = createFetchActions(
    'public-share/deleted',
    (state, {id}) => {
        let request = api.deletePublicShare({id});

        request.then(({success}) => {
            if (success) {
                showNotification({
                    type: NOTIFICATION_TYPES.SUCCESS,
                    translation: "public-share.deleted"
                });
            }
        });

        return request;
    }
);


