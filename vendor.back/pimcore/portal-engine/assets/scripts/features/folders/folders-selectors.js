/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";

export const getByPath = (state, path) => state.byPath[path];
export const isOpenByPath = (state, path) => state.collapseStateByPath[path] || false;
export const getAllPaths = (state) => state.allPaths;

export function getAllChildrenByFolder(state, path) {
    return getAllPaths(state)
            .filter(id => {
                let node = getByPath(state, id);
                return node && node.parent === path
            })
        || [];
}

export const getChildrenPageFetchingState = (state, path, pageNumber) => {
    if (!state.childrenFetchingStateByPage[path]) {
        return NOT_ASKED;
    } else {
        return state.childrenFetchingStateByPage[path][pageNumber] || NOT_ASKED;
    }
};

export const hasMoreChildrenByPath = (state, path) =>
    state.paginationByPath[path]
        ? state.paginationByPath[path].lastPage > state.paginationByPath[path].currentPage
        : false;

export const getCurrentPageByPath = (state, path) => {
    if (!state.paginationByPath[path]) {
        return 1;
    } else {
        return state.paginationByPath[path].currentPage || 1;
    }
};