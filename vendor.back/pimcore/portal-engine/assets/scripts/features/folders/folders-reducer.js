/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {createReducer} from "@reduxjs/toolkit";
import {FAILED, FETCHING, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import {arrayToObject} from "~portal-engine/scripts/utils/utils";
import {
    FOLDER_CHILDREN_PAGE_FAILED,
    FOLDER_CHILDREN_PAGE_FETCHED,
    FOLDER_CHILDREN_PAGE_REQUESTED,
    toggleFolderCollapseState,
} from "~portal-engine/scripts/features/folders/folders-actions";
import {getConfig} from "~portal-engine/scripts/utils/general";
import {urlChanged} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";

const initialState = {
    byPath: {},
    collapseStateByPath: {},
    allPaths: [],
    paginationByPath: {},
    childrenFetchingStateByPage: {}
};

let folderConfig = getConfig('list.folders');
if (folderConfig && folderConfig.root) {
    initialState.allPaths.push(folderConfig.root.path);
    initialState.byPath[folderConfig.root.path] = {
        name: folderConfig.root.name,
        path: folderConfig.root.path,
        hasChildren: true,
        parent: null
    };

    if ( folderConfig.active) {
        initialState.collapseStateByPath[folderConfig.root.path] = true;
    }
}

export default createReducer(initialState, {
    [FOLDER_CHILDREN_PAGE_REQUESTED]: function (state, {payload: {path, page}}) {
        state.childrenFetchingStateByPage[path] = {
            ...state.childrenFetchingStateByPage[path],
            [page]: FETCHING
        };

        state.paginationByPath[path] = {
            ...state.paginationByPath[path],
            currentPage: page,
        };
    },
    [FOLDER_CHILDREN_PAGE_FETCHED]: function (state, {payload: {path, page, items, pageSize, totalResults }}) {
        state.childrenFetchingStateByPage[path] = {
            ...state.childrenFetchingStateByPage[path],
            [page]: SUCCESS
        };

        state.paginationByPath[path] = {
            currentPage: page,
            lastPage: Math.ceil(totalResults / pageSize)
        };

        let normalizedItems = normalize(items, path);
        state.byPath = {
            ...state.byPath,
            ...arrayToObject(normalizedItems, 'path')
        };
        state.allPaths = [
            ...state.allPaths,
            ...normalizedItems.map(({path}) => path)
        ]

    },
    [FOLDER_CHILDREN_PAGE_FAILED]: function (state, {payload: {path, page}}) {
        state.childrenFetchingStateByPage[path] = {
            ...state.childrenFetchingStateByPage[path],
            [page]: FAILED
        }
    },
    [toggleFolderCollapseState]: function (state, {payload: {path, state: currentState}}) {
        // state.byPath[path].isOpen = currentState;
        state.collapseStateByPath[path] = currentState;
    },
    [urlChanged]: urlChangedReducer,
});

function urlChangedReducer(state, {payload: {params}}) {
    let activeFolder = params.filter(([name]) => name === 'folder').map(([_, value]) => value)[0];
    if (activeFolder) {
        let root = getConfig('list.folders.root.path');
        let subFolders = activeFolder.replace(`${root}/`, '').split('/');

        subFolders.reduce((absolutePath, subFolder) => {
            state.collapseStateByPath[`${absolutePath}`] = true;
            return `${absolutePath}/${subFolder}`;
        }, root);
    }
}

function normalize(items = [], parentFolder = null) {
    return items.flatMap(function ({items, hasItems, ...node}) {
        let currentPath = `${parentFolder}/${node.name}`;
        return [
            {
                ...node,
                hasChildren: hasItems,
                path: currentPath,
                parent: parentFolder,
            },
            ...(items ? normalize(items, currentPath) : [])
        ];
    })
}