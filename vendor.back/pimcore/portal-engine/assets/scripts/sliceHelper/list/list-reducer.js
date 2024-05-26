/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {DEFAULT_PAGE} from "~portal-engine/scripts/consts";
import {arrayToObject, deserializeParamsToObject, identity} from "~portal-engine/scripts/utils/utils";
import {FAILED, FETCHING, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";

export const initialState = {
    currentPage: DEFAULT_PAGE,
    pageSize: 60,
    pageCount: 0,
    resultCount: 0,
    idsByPages: {},
    itemsById: {},
    fetchingStateByPage: {},
    fetchingMessageByPage: {},
};

export function createReducer({
    ACTION_TYPES,
    payloadMapper = identity
}) {
    return {
        [ACTION_TYPES.PAGE_REQUESTED]: (state, {payload: {params}}) => {
            let pageParam = params.find(([key]) => key === 'page');
            let page = pageParam ? pageParam[1] : DEFAULT_PAGE;
            state.fetchingStateByPage[page] = FETCHING;
            state.fetchingMessageByPage[page] = null;
        },
        [ACTION_TYPES.PAGE_FETCH_SUCCEEDED]: function (state, {payload}) {
            const {data: {entries, page, pageSize, pages, total, totalResults, uploadFolder}} = payloadMapper(payload);

            state.currentPage = page;
            state.pageSize = pageSize;
            state.pageCount = pages;
            state.resultCount = totalResults || total || 0;
            state.fetchingStateByPage[page] = SUCCESS;
            state.fetchingMessageByPage = {};
            state.idsByPages[page] = entries.map(filter => filter.id);
            state.itemsById = {
                ...state.itemsById,
                ...arrayToObject(entries),
            };

            state.uploadFolder = uploadFolder;
        },
        [ACTION_TYPES.PAGE_FETCH_FAILED]: function (state, {payload: {page = DEFAULT_PAGE, error}}) {
            state.fetchingStateByPage[page] = FAILED;
            state.fetchingMessageByPage[page] = error;
        },
        [ACTION_TYPES.PAGE_CHANGED]: function (state, {payload: {page}}) {
            state.currentPage = page;
        },
        [ACTION_TYPES.URL_CHANGED]: function (state, {payload: {params}}) {
            let {page} = deserializeParamsToObject(params);

            state.currentPage = page
                ? +page
                : DEFAULT_PAGE;
        }
    };
}