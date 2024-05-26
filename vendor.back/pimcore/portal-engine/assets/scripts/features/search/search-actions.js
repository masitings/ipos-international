/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from "react";
import {createAction} from "@reduxjs/toolkit";
import {createFetchActions} from "~portal-engine/scripts/utils/fetch";
import {createActionCreators} from "~portal-engine/scripts/sliceHelper/list/list-actions";
import * as searchSelectors from "~portal-engine/scripts/features/search/search-selectors";
import {getSearchList, fetchSearchResult} from "~portal-engine/scripts/features/search/search-api";
import * as api from "~portal-engine/scripts/features/search/search-api";
import {showNotification} from "~portal-engine/scripts/features/notifications/notifications-actions";
import * as NOTIFICATION_TYPES from "~portal-engine/scripts/consts/notification-types";
import {trans} from "~portal-engine/scripts/utils/intl";

// Search list
export const {
    ACTION_TYPES: SEARCH_LIST_TYPES,
    actionCreators: listActionCreators,
} = createActionCreators({
    actionTypePrefix: 'search/search-list',
    api: {fetchList: getSearchList},
    selectors: searchSelectors
});

export const {
    urlChanged,
    setup,
    changePage,
    requestListPage
} = listActionCreators;

export const {
    actionTypes: DELETED_SEARCH_TYPES,
    actionCreator: deleteSearch
} = createFetchActions(
    'search/deleted',
    (state, {id}) => {
        const search = searchSelectors.getItemById(state, id);
        let request

        if(search.owner) {
            request = api.deleteSearch({id});
        } else if(search.sharedWith === "user") {
            request = api.deleteSharedSearch({id});
        }

        request.then(({success}) => {
            if (success) {
                showNotification({
                    type: NOTIFICATION_TYPES.SUCCESS,
                    translation: "search.deleted"
                });
            }
        });

        return request;
    }
);

let currentSearchRequest;
export const requestSearch = function (params) {
        if (currentSearchRequest && currentSearchRequest.abort) {
            currentSearchRequest.abort();
        }

        currentSearchRequest = fetchSearchResult(params);
        return currentSearchRequest.response.then(function ({success, data, error}) {
            currentSearchRequest = null;

            if (success && data) {
                return data;
            } else {
                return Promise.reject(error);
            }
        }).catch(error => {
            console.error(error);

            currentSearchRequest = null;
        })
};

export const {
    actionTypes: RENAMED_SEARCH_TYPES,
    actionCreator: renameSearch
} = createFetchActions(
    'search/renamed',
    (state, {id, name}) => {
        let request = api.renameSearch({id, name});

        request.then(({success}) => {
            if (success) {
                showNotification({
                    type: NOTIFICATION_TYPES.SUCCESS,
                    translation: "search.renamed"
                });
            }
        });

        return request;
    }
);


export const {
    actionTypes: SAVE_SEARCH_TYPES,
    actionCreator: saveSearch
} = createFetchActions(
    'search/save',
    (state, {urlQuery, name}) => {
        let request = api.saveSearch({urlQuery, name});
        let translationRequest = trans('search.save-search');

        Promise.all([request, translationRequest]).then(([{success, data: {name}}, translation]) => {
            if (success) {
                showNotification({
                    type: NOTIFICATION_TYPES.SUCCESS,
                    message: translation.replace('[name]', name)
                });
            }
        });

        return request;
    }
);

// sharing
export const {
    actionTypes: SEARCH_SHARE_LIST_REQUEST_TYPES,
    actionCreator: requestSearchShareList
} = createFetchActions(
    'search/share-list',
    (state, {searchId}) => api.getSearchShareList({searchId}).response);

export const {
    actionTypes: UPDATE_SEARCH_SHARE_LIST_TYPES,
    actionCreator: updateSearchShareList
} = createFetchActions(
    'search/share-list/changed',
    (state, {searchId, shares}) => {
        let request = api.updateSearchShareList({searchId, shares});

        request.then(({success}) => {
            if (success) {
                showNotification({
                    type: NOTIFICATION_TYPES.SUCCESS,
                    translation: "search.permission.updated"
                });
            }
        });

        return request;
    });