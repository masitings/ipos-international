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
import {createFetchActions, errorToObject, fetchJson, getEndpoint} from "~portal-engine/scripts/utils/fetch";
import * as api from "~portal-engine/scripts/features/asset/asset-api";
import {
    applyWorkflowTransition as applyWorkflowTransitionApi,
    fetchMetadataLayout as fetchMetadataLayoutApi,
    fetchVersionComparison,
    fetchVersionHistory as doFetchVersionHistory,
    fetchWorkflow as fetchWorkflowApi,
    getDirectEditUrl,
    openDirectEdit,
    publishVersion as doPublishVersion,
    saveMetadata as doSaveMetadata
} from "~portal-engine/scripts/features/asset/asset-api";
import {fetchValidLanguages} from "~portal-engine/scripts/features/element/element-api";
import {setLanguageConfig, setValidLanguages} from "~portal-engine/scripts/features/element/element-layout";
import {setEditableLanguages} from "~portal-engine/scripts/features/asset/asset-layout";
import {fetchTasks} from "~portal-engine/scripts/features/tasks/tasks-actions";
import {showNotification} from "~portal-engine/scripts/features/notifications/notifications-actions";
import * as NOTIFICATION_TYPES from "~portal-engine/scripts/consts/notification-types";
import {
    getAssetId,
    getDirectEditMessage,
    getMetadataEditDataById,
    getMetadataEditValidationById,
    getSelectedVersionIds
} from "~portal-engine/scripts/features/asset/asset-selectors";
import {store} from "~portal-engine/scripts/store";
import {showError} from "~portal-engine/scripts/utils/general";
import {ERROR, LISTENING, UPDATING} from "~portal-engine/scripts/consts/direct-edit-status";

export const setupAsset = createAction("asset/setup");

export const resetDetail = createAction("asset/detail/reset");
export const detailRequested = createAction("asset/detail/requested");
export const detailFetched = createAction("asset/detail/fetched");
export const detailFailed = createAction("asset/detail/failed");

export function fetchDetail() {
    return function (dispatch) {
        dispatch(detailRequested());

        Promise.all([
            fetchJson(getEndpoint("detail")),
        ]).then(([detail]) => {
            dispatch(detailFetched({
                detail: detail.data
            }));

            dispatch(workflowFetched(detail.data.workflow));
        }).catch((error) => {
            dispatch(detailFailed(errorToObject(error)));
        });
    }
}

export const metadataLayoutRequested = createAction("asset/metadata/layout/requested");
export const metadataLayoutFetched = createAction("asset/metadata/layout/fetched");
export const metadataLayoutFailed = createAction("asset/metadata/layout/failed");

export function fetchMetadataLayout() {
    return function (dispatch) {
        dispatch(metadataLayoutRequested());

        Promise.all([
            fetchMetadataLayoutApi(),
            fetchValidLanguages()
        ])
            .then(([metadataResponse, languagesResponse]) => {
                setValidLanguages(languagesResponse.data.visible);
                setEditableLanguages(languagesResponse.data.editable);
                setLanguageConfig(languagesResponse.data.config);

                dispatch(metadataLayoutFetched(metadataResponse.data));
            })
            .catch((error) => {
                dispatch(metadataLayoutFailed(errorToObject(error)));
            });
    }
}

export const editMetadata = createAction("asset/metadata/edit");
export const updateMetadata = createAction("asset/metadata/update");
export const toggleKeepMetadata = createAction("asset/metadata/keep");
export const addMetadata = createAction("asset/metadata/add");
export const removeMetadata = createAction("asset/metadata/remove");
export const validateMetadata = createAction("asset/metadata/validate");

export function checkMetadata({id}) {
    return (dispatch, getState) => {
        dispatch(validateMetadata({id}));

        const state = getState();
        const validation = getMetadataEditValidationById(state, id);

        return new Promise((resolve, reject) => {
            if (validation.invalidFields.length === 0) {
                resolve();
            } else {
                reject();
            }
        });
    }
}

export function saveMetadata({id}) {
    return (dispatch, getState) => {
        dispatch(validateMetadata({id}));

        const state = getState();
        const validation = getMetadataEditValidationById(state, id);

        if (!validation.invalidFields.length) {
            return doSaveMetadata(id, getMetadataEditDataById(state, id));
        } else {
            return new Promise(resolve => resolve());
        }
    }
}

export const editMetaDataClicked = createAction('asset/metadata/edit/modal-open', ({dataPoolId, ids}) => ({
    payload: {
        dataPoolId,
        ids
    }
}));

export const closeEditMetaDataModal = createAction('asset/metadata/edit/modal-closed');

export const {
    actionTypes: BATCH_META_DATA_TYPE,
    actionCreator: batchMetaData,
} = createFetchActions(
    `asset/metadata/edit`,
    (state, params, dispatch) => {
        let batchMetaDataRequest = api.batchMetaData(params).response;

        batchMetaDataRequest.then(() => {
            dispatch(fetchTasks())
        });

        return batchMetaDataRequest;
    }, (state, payload) => ({
        ...payload
    })
);

export const updateDetailTags = createAction("asset/detail/tags/update");

export const workflowRequested = createAction("asset/workflow/requested");
export const workflowFetched = createAction("asset/workflow/fetched");
export const workflowFailed = createAction("asset/workflow/failed");
export const workflowUncollapse = createAction("asset/workflow/uncollapse");

export function fetchWorkflow() {

    return function (dispatch) {
        dispatch(workflowRequested());

        Promise.all([
            fetchWorkflowApi(getAssetId(store.getState()))
        ])
            .then(([response]) => {
                dispatch(workflowFetched(response.data));
            })
            .catch((error) => {
                dispatch(workflowFailed(errorToObject(error)));
            });
    }
}

export const openWorkflowTransitionModal = createAction("asset/workflow/open-modal");
export const closeWorkflowTransitionModal = createAction("asset/workflow/close-modal");
export const openWorkflowHistoryModal = createAction("asset/workflow/open-history-modal");
export const closeWorkflowHistoryModal = createAction("asset/workflow/close-history-modal");

export const {
    actionTypes: APPLY_WORKFLOW_TRANSITION_TYPES,
    actionCreator: applyWorkflowTransition
} = createFetchActions(
    'asset/workflow/apply-transition',
    (state, {workflow, transition, type, data}, dispatch) => {
        dispatch(workflowRequested());

        let request = applyWorkflowTransitionApi(getAssetId(state), workflow.name, transition.name, type, data);

        request.then(({success}) => {
            if (success) {
                showNotification({
                    type: NOTIFICATION_TYPES.SUCCESS,
                    translation: "workflow.transition-applied"
                });
            }

        }).finally(() => {
            dispatch(fetchWorkflow());
            dispatch(workflowUncollapse());
        });

        return request;
    }
);

export const versionHistoryRequested = createAction("asset/versions/requested");
export const versionHistoryFetched = createAction("asset/versions/fetched");
export const versionHistoryFailed = createAction("asset/versions/failed");

export function fetchVersionHistory() {
    return (dispatch, getState) => {
        dispatch(versionHistoryRequested());

        doFetchVersionHistory(getAssetId(getState()))
            .then((response) => {
                dispatch(versionHistoryFetched(response.data));
            })
            .catch((error) => {
                dispatch(versionHistoryFailed(errorToObject(error)));
            });
    };
}

export const toggleVersionSelection = createAction("asset/versions/selection-toggled");

export const versionComparisonRequested = createAction("asset/versions/comparison/requested");
export const versionComparisonFetched = createAction("asset/versions/comparison/fetched");
export const versionComparisonFailed = createAction("asset/versions/comparison/failed");

export function fetchComparisonForSelectedVersions() {
    return function (dispatch, getState) {
        const state = getState();

        dispatch(versionComparisonRequested());

        fetchVersionComparison(getAssetId(state), getSelectedVersionIds(state)).then(({success, data, error}) => {
            if (success) {
                dispatch(versionComparisonFetched(data))
            } else {
                Promise.reject(error);
            }
        }).catch((error) => {
            console.error(error);

            dispatch(versionComparisonFailed(error));
        });
    }
}

export function publishVersion({id}) {
    return function (dispatch, getState) {
        return doPublishVersion(getAssetId(getState()), id)
            .then(() => {
                dispatch(fetchVersionHistory())
            })
            .catch(showError);
    }
}

export const updateDirectEditStatus = createAction("asset/direct-edit/status");

export function startDirectEdit() {
    return (dispatch, getState) => {
        openDirectEdit(getAssetId(getState()))
            .then((data) => {
                if (data.desktopOpenLink && data.desktopOpenLink.length > 0) {
                    window.location.href = data.desktopOpenLink;
                    dispatch(updateDirectEditStatus({status: LISTENING, message: data}));
                } else {
                    dispatch(updateDirectEditStatus({status: ERROR, message: data}));
                }
            })
            .catch(() => {
                showError();
            })
    }
}

export function cancelDirectEdit() {
    return (dispatch, getState) => {
        const message = getDirectEditMessage(getState());

        if (message && message.cancelBtnLink) {
            // cancel url if message is available
            fetchJson(getDirectEditUrl(message.cancelBtnLink, getAssetId(getState())), {}, true);
        }

        dispatch(updateDirectEditStatus({status: null, message: null}));
    }
}

export function updateFromDirectEdit() {
    return (dispatch, getState) => {
        const message = getDirectEditMessage(getState());

        if (message && message.successBtnActive) {
            dispatch(updateDirectEditStatus({status: UPDATING, message: message}));

            fetchJson(getDirectEditUrl(message.successBtnLink, getAssetId(getState())), {}, true)
                .then(() => {
                    dispatch(fetchDetail())
                })
                .catch(showError)
                .finally(() => {
                    dispatch(cancelDirectEdit());
                });
        }
    };
}