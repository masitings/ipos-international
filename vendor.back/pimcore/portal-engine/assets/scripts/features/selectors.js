/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


// Folder
import {
    getChildrenPageFetchingState,
    getAllChildrenByFolder, getByPath, isOpenByPath, hasMoreChildrenByPath, getCurrentPageByPath, getSelectedPath
} from "~portal-engine/scripts/features/folders/folders-selectors";

export const getFolderByPath = (state, path) => getByPath(state.folders, path);
export const getAllChildFolderPathsByPath = (state, path) => getAllChildrenByFolder(state.folders, path);
export const getChildFolderPageFetchingState = (state, path, pageNumber) => getChildrenPageFetchingState(state.folders, path, pageNumber);
export const isFolderOpenByPath = (state, path) => isOpenByPath(state.folders, path);
export const hasMoreChildFoldersByPath = (state, path) => hasMoreChildrenByPath(state.folders, path);
export const getChildFolderPageByPath = (state, path) => getCurrentPageByPath(state.folders, path);


// Tags
import {
    getById, getChildIdsById, getFetchingState, getUnfilteredFetchingState, isOpenById,
} from "~portal-engine/scripts/features/tags/tags-selectors";

export const getTagsFetchingState = state => getFetchingState(state.tags);
export const getUnfilteredTagsFetchingState = state => getUnfilteredFetchingState(state.tags);
export const getTagById = (state, id) => getById(state.tags, id);
export const getChildTagIdsById = (state, parentId) => getChildIdsById(state.tags, parentId);
export const isTagOpenById = (state, id) => isOpenById(state.tags, id);


// Downlaod
import * as downloadSelectors from "~portal-engine/scripts/features/download/download-selectors";

export const getDownloadConfigModalDataPoolId = state => downloadSelectors.getConfigModalDataPoolId(state.download);
export const getDownloadConfigModalIds = state => downloadSelectors.getConfigModalIds(state.download);
export const getDownloadConfigModalMode = state => downloadSelectors.getConfigModalMode(state.download);

export const getDownloadConfigFetchErrorByDataPoolId = (state, dataPoolId) => downloadSelectors.getConfigFetchErrorByDataPoolId(state.download, dataPoolId);
export const getDownloadConfigFetchStateByDataPoolId = (state, dataPoolId) => downloadSelectors.getConfigFetchStateByDataPoolId(state.download, dataPoolId);
export const getDownloadAttributeIdsByDataPoolId = (state, dataPoolId) => downloadSelectors.getAllAttributeIdsByDataPoolId(state.download, dataPoolId);
export const getDownloadAttributeById = (state, {id, dataPoolId}) => downloadSelectors.getAttributeById(state.download, {id, dataPoolId});

import {selectors as downloadListSelectors} from "~portal-engine/scripts/sliceHelper/list/list-selectos";
export const getDownloadListPageNumber = (state, ...rest) => downloadListSelectors.getCurrentPageNumber(state.download, ...rest);
export const getDownloadListPageCount = (state, ...rest) => downloadListSelectors.getPageCount(state.download, ...rest);
export const getDownloadListIdsByPageNumber = (state, ...rest) => downloadListSelectors.getIdsByPageNumber(state.download, ...rest);
export const getDownloadListItemById = (state, ...rest) => downloadListSelectors.getItemById(state.download, ...rest);
export const getDownloadListFetchingStateByPageNumber = (state, ...rest) => downloadListSelectors.getFetchingStateByPage(state.download, ...rest);
export const getDownloadListResultCount = (state, ...rest) => downloadListSelectors.getResultCount(state.download, ...rest);
export const getDownloadListPageSize = (state, ...rest) => downloadListSelectors.getPageSize(state.download, ...rest);
export const getDownloadListMessageByPageNumber = (state, ...rest) => downloadListSelectors.getFetchingMessageByPage(state.download, ...rest);
export const getDownloadListParams = (state, ...rest) => downloadListSelectors.getListParams(state.download, ...rest);
export const getDownloadListParamNames = (state, ...rest) => downloadListSelectors.getListParamNames(state.download, ...rest);