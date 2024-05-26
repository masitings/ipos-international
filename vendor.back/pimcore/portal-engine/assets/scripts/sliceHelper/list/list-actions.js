/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {createFetchActions} from "~portal-engine/scripts/utils/fetch";
import {createAction} from "@reduxjs/toolkit";

export function createActionCreators({actionTypePrefix, api, selectors}) {
    const {
        actionTypes: LIST_PAGE_FETCHING_TYPES,
        actionCreator: requestListPage,
    } = createFetchActions(
        `${actionTypePrefix}/list/page`,
        (state, payload) => api.fetchList(payload.params).response,
        state => ({
            params: selectors.getListParams(state) || []
        })
    );

    const ACTION_TYPES = {
        PAGE_REQUESTED: LIST_PAGE_FETCHING_TYPES.REQUESTED,
        PAGE_FETCH_SUCCEEDED: LIST_PAGE_FETCHING_TYPES.SUCCEEDED,
        PAGE_FETCH_FAILED: LIST_PAGE_FETCHING_TYPES.FAILED,
        PAGE_CHANGED: `${actionTypePrefix}/list/page-changed`,
        URL_CHANGED: `${actionTypePrefix}/list/url-changed`,
        // todo ?
        SETUP: `${actionTypePrefix}/list/url-changed`,
    };

    const urlChanged = createAction(ACTION_TYPES.URL_CHANGED, (params) => ({
        payload: {params}
    }));

    const setup = urlChanged;

    const changePage = (page) => {
        return ({
            type: ACTION_TYPES.PAGE_CHANGED,
            payload: {
                page
            }
        });
    };

    return {
        ACTION_TYPES,
        actionCreators: {
            changePage,
            setup,
            urlChanged,
            requestListPage
        }
    }
}