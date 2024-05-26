/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {configureStore} from "@reduxjs/toolkit";
import {combineReducers} from "redux";
import assetReducer from "~portal-engine/scripts/features/asset/asset-reducer";
import dataPoolListReducer from "~portal-engine/scripts/features/data-pool-list/data-pool-list-reducer";
import dataObjectReducer from "~portal-engine/scripts/features/data-objects/data-object-reducer";
import downloadReducer from "~portal-engine/scripts/features/download/download-reducer";
import uploadReducer from "~portal-engine/scripts/features/upload/upload-reducer";
import tagsReducer from "~portal-engine/scripts/features/tags/tags-reducer";
import foldersReducer from "~portal-engine/scripts/features/folders/folders-reducer";
import tasksReducer from "~portal-engine/scripts/features/tasks/tasks-reducer";
import notificationsReducer from "~portal-engine/scripts/features/notifications/notifications-reducer";
import collectionsReducer from "~portal-engine/scripts/features/collections/collections-reducer";
import searchReducer from "~portal-engine/scripts/features/search/search-reducer";
import subfolderReducer from "~portal-engine/scripts/features/subfolder/subfolder-reducer";
import publicShareReducer from "~portal-engine/scripts/features/public-share/public-share-reducer";

export const store = configureStore({
    reducer: combineReducers({
        asset: assetReducer,
        dataObject: dataObjectReducer,
        download: downloadReducer,
        upload: uploadReducer,
        tags: tagsReducer,
        folders: foldersReducer,
        tasks: tasksReducer,
        notifications: notificationsReducer,
        collections: collectionsReducer,
        search: searchReducer,
        dataPoolList: dataPoolListReducer,
        subfolder: subfolderReducer,
        publicShare: publicShareReducer
    })
});

export const dispatch = store.dispatch;

// todo delete debug only
window.store = store;
