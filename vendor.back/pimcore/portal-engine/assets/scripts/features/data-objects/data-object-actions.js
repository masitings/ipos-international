/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {getDataPoolId, getSelectedVersionIds} from "~portal-engine/scripts/features/data-objects/data-object-selectors";
import {fetchJson, getEndpoint, errorToPayload, buildParams} from "~portal-engine/scripts/utils/fetch";
import {fetchValidLanguages} from "~portal-engine/scripts/features/element/element-api";
import {setValidLanguages, setLanguageConfig} from "~portal-engine/scripts/features/element/element-layout";
import {
    setCurrentLayout,
    addClassificationStoreLayoutDefinitions,
    addFieldcollectionLayoutDefinitions,
    addObjectbrickLayoutDefinitions
} from "~portal-engine/scripts/features/data-objects/object-layout";

export const SETUP = "data-object/setup";

export function setupDataObject(dataObjectId, dataPoolId, versionsEnabled) {
    return {
        type: SETUP,
        payload: {
            dataObjectId: dataObjectId,
            dataPoolId: dataPoolId,
            versionsEnabled: versionsEnabled
        }
    };
}

export const DETAIL_REQUESTED = "data-object/detail-requested";
export const DETAIL_FETCHED = "data-object/detail-fetched";
export const DETAIL_FAILED = "data-object/detail-failed";

export function fetchDetail() {
    return function (dispatch, getState) {
        dispatch({type: DETAIL_REQUESTED});

        Promise.all([
            fetchJson(getEndpoint("detail")),
            fetchValidLanguages()
        ]).then(([detail, validLanguages]) => {
            // don't store these informations in the state, they are to big
            setCurrentLayout(detail.data.layout);
            setValidLanguages(validLanguages.data.visible);
            setLanguageConfig(validLanguages.data.config);
            addFieldcollectionLayoutDefinitions(detail.data.fieldcollectionLayouts);
            addObjectbrickLayoutDefinitions(detail.data.objectbrickLayouts);
            addClassificationStoreLayoutDefinitions(detail.data.classificationStoreLayouts);

            dispatch(detailFetched(detail.data, validLanguages.data));
        }).catch((error) => {
            dispatch({type: DETAIL_FAILED, ...errorToPayload(error)});
        });
    }
}

export function detailFetched(detail, validLanguages) {
    return {
        type: DETAIL_FETCHED,
        payload: {
            detail: detail,
            validLanguages: validLanguages
        }
    }
}

export const VERSION_LIST_REQUESTED = "data-object/version/list-requested";
export const VERSION_LIST_FETCHED = "data-object/version/list-fetched";
export const VERSION_LIST_FAILED = "data-object/version/list-failed";

export function fetchVersions() {
    return function (dispatch, getState) {
        const state = getState();

        dispatch({type: VERSION_LIST_REQUESTED});

        fetchJson(getEndpoint("versionHistory"))
            .then(({success, data, error}) => {
                if (success) {
                    dispatch({
                        type: VERSION_LIST_FETCHED,
                        payload: data
                    });
                } else {
                    Promise.reject(error);
                }
            })
            .catch((error) => {
                console.error(error);

                dispatch({type: VERSION_LIST_FAILED, payload: error});
            });
    }
}

export const VERSION_SELECTION_TOGGLED = "data-object/versions/selection-toggled";

export function toggleVersionSelection(id, isSelected) {
    return {
        type: VERSION_SELECTION_TOGGLED,
        payload: {
            id: id,
            isSelected: isSelected
        }
    };
}

export const VERSION_COMPARISON_REQUESTED = "data-object/version/comparison-requested";
export const VERSION_COMPARISON_FETCHED = "data-object/version/comparison-fetched";
export const VERSION_COMPARISON_FAILED = "data-object/version/comparison-failed";

export function fetchComparisonForSelectedVersions() {
    return function (dispatch, getState) {
        const state = getState();

        dispatch({type: VERSION_COMPARISON_REQUESTED});

        fetchJson(getEndpoint("versionComparison") + "?" + buildParams({
            dataPoolId: getDataPoolId(state),
            ids: getSelectedVersionIds(state)
        })).then(({success, data, error}) => {
            if (success) {
                dispatch({
                    type: VERSION_COMPARISON_FETCHED,
                    payload: data
                });
            } else {
                Promise.reject(error);
            }
        }).catch((error) => {
            console.error(error);

            dispatch({type: VERSION_COMPARISON_FAILED, payload: error});
        });
    }
}