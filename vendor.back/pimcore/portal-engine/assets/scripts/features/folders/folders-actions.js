/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {showError} from "~portal-engine/scripts/utils/general";
import {fetchFolders} from "~portal-engine/scripts/features/folders/folders-api";
import {createAction} from "@reduxjs/toolkit";

export const FOLDER_CHILDREN_PAGE_REQUESTED = "folders/children-page-requested";
export const FOLDER_CHILDREN_PAGE_FETCHED = "folders/children-page-fetched";
export const FOLDER_CHILDREN_PAGE_FAILED = "folders/children-page-failed";

let currentFolderRequest;
export const requestFolderChildrenPage = (path, page = 1) => {
    if (currentFolderRequest && currentFolderRequest.abort) {
        currentFolderRequest.abort();
    }

    currentFolderRequest = fetchFolders(path, page);
    return (dispatch) => {
        dispatch({
            type: FOLDER_CHILDREN_PAGE_REQUESTED,
            payload: {
                path,
                page
            }
        });

        currentFolderRequest.response.then(function ({success, data, error}) {
            if (success && data) {
                dispatch({
                    type: FOLDER_CHILDREN_PAGE_FETCHED,
                    payload: {
                        path,
                        page,
                        ...data
                    }
                });
            } else {
                return Promise.reject(error);
            }
        }).catch((error) => {
            showError(error);

            dispatch({
                type: FOLDER_CHILDREN_PAGE_FAILED,
                payload: {
                    path,
                    page,
                    error
                }
            })
        })
    }
};

export const toggleFolderCollapseState = createAction('folders/toggled-collapse-state');


// let currentRelocateFolderRequest;
// export const requestRelocateFolderChildrenPage = function (path, page = 1) {
//     if (currentRelocateFolderRequest && currentRelocateFolderRequest.abort) {
//         currentRelocateFolderRequest.abort();
//     }
//
//     currentRelocateFolderRequest = fetchRelocateFolders(path, page);
//     return currentRelocateFolderRequest.response.then(function ({success, data, error}) {
//         currentRelocateFolderRequest = null;
//
//         if (success && data) {
//             return data;
//         } else {
//             return Promise.reject(error);
//         }
//     }).catch(error => {
//         console.error(error);
//
//         currentRelocateFolderRequest = null;
//     })
// };