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
import {NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import {
    setupAsset,
    detailRequested,
    detailFetched,
    detailFailed,
    resetDetail,
    metadataLayoutRequested,
    metadataLayoutFetched,
    metadataLayoutFailed,
    workflowRequested,
    workflowFetched,
    workflowFailed,
    workflowUncollapse,
    editMetadata,
    validateMetadata,
    updateMetadata,
    addMetadata,
    removeMetadata,
    editMetaDataClicked,
    closeEditMetaDataModal,
    updateDetailTags,
    toggleKeepMetadata,
    versionHistoryRequested,
    versionHistoryFetched,
    versionHistoryFailed,
    toggleVersionSelection,
    versionComparisonRequested,
    versionComparisonFetched,
    versionComparisonFailed,
    openWorkflowTransitionModal,
    closeWorkflowTransitionModal,
    openWorkflowHistoryModal,
    closeWorkflowHistoryModal,
    updateDirectEditStatus,
    BATCH_META_DATA_TYPE
} from "~portal-engine/scripts/features/asset/asset-actions"
import {fetchingStateReducer} from "~portal-engine/scripts/utils/fetch";
import {LOCALIZED_FIELDS} from "~portal-engine/scripts/consts/layout";
import {getValidLanguages} from "~portal-engine/scripts/features/element/element-layout";
import {LISTENING} from "~portal-engine/scripts/consts/direct-edit-status";

const initialState = {
    assetId: null,
    detailFetchingState: NOT_ASKED,
    detailError: null,
    detail: {
        data: null
    },
    versionHistoryFetchingState: NOT_ASKED,
    versionHistoryError: null,
    versionHistory: null,
    selectedVersionIds: [],
    versionComparisonFetchingState: NOT_ASKED,
    versionComparisonError: null,
    versionComparison: {},
    metadataLayoutFetchingState: NOT_ASKED,
    metadataLayoutError: null,
    metadataLayout: {},
    metadataEdit: {},
    editMetaDataModalOpen: false,
    workflowFetchingState: NOT_ASKED,
    workflowModalOpen: false,
    workflowPanelCollapsed: true,
    currentWorkflowTransition: null,
    directEditStatus: null,
    directEditMessage: null
};

function validateLayout(prefix, layout, data, validation) {
    if (!layout) {
        return;
    }

    if (layout.mandatory) {
        let valid = false;

        if (data.metadata[prefix][layout.name]) {
            valid = true;
        }

        if (data.metadata[prefix][LOCALIZED_FIELDS] && data.metadata[prefix][LOCALIZED_FIELDS][layout.name]) {
            valid = true;

            getValidLanguages().forEach((language) => {
                if (!data.metadata[prefix][LOCALIZED_FIELDS][layout.name][language]) {
                    valid = false;
                }
            });
        }

        if (!valid) {
            validation.invalidPrefixes.push(prefix);
            validation.invalidFields.push({
                prefix: prefix,
                name: layout.name
            });
        }
    }

    if (Array.isArray(layout.childs)) {
        layout.childs.forEach((child) => {
            validateLayout(prefix, child, data, validation);
        });
    }
}

export default createReducer(initialState, {
    [setupAsset.type]: (state, {payload}) => ({
        ...state,
        assetId: payload.assetId
    }),

    [resetDetail.type]: (state) => {
        return {
            ...state,
            detailFetchingState: NOT_ASKED
        };
    },

    [validateMetadata.type]: (state, {payload}) => {
        const data = state.metadataEdit[payload.id];
        const layout = state.metadataLayout;
        const validation = {
            invalidFields: [],
            invalidPrefixes: []
        };

        Object.entries(layout).forEach(([prefix, layout]) => {
            if (data.removed.includes(prefix) || !data.metadata[prefix]) {
                return;
            }

            validateLayout(prefix, layout, data, validation);
        });

        return {
            ...state,
            metadataEdit: {
                ...state.metadataEdit,
                [payload.id]: {
                    ...state.metadataEdit[payload.id],
                    validation: validation
                }
            }
        }
    },

    [editMetadata.type]: (state, {payload}) => ({
        ...state,
        metadataEdit: {
            ...state.metadataEdit,
            [payload.id]: {
                metadata: payload.metadata,
                removed: [],
                meta: {},
                validation: {
                    invalidFields: [],
                    invalidPrefixes: []
                }
            }
        }
    }),

    [addMetadata.type]: (state, {payload}) => ({
        ...state,
        metadataEdit: {
            ...state.metadataEdit,
            [payload.id]: {
                ...state.metadataEdit[payload.id],
                metadata: {
                    ...state.metadataEdit[payload.id].metadata,
                    [payload.prefix]: state.metadataEdit[payload.id].metadata[payload.prefix] || {},
                },
                removed: state.metadataEdit[payload.id].removed.filter(key => key !== payload.prefix)
            }
        }
    }),

    [removeMetadata.type]: (state, {payload}) => {
        return {
            ...state,
            metadataEdit: {
                ...state.metadataEdit,
                [payload.id]: {
                    ...state.metadataEdit[payload.id],
                    removed: state.metadataEdit[payload.id].removed.concat([payload.prefix])
                }
            }
        };
    },

    [updateMetadata.type]: (state, {payload}) => {
        // check if the prefixed configuration is already available
        let prefixedMetadata = state.metadataEdit[payload.id].metadata[payload.prefix];

        // create new if not available
        if (!prefixedMetadata) {
            prefixedMetadata = {};
        }

        if (payload.language) {
            if (!prefixedMetadata[LOCALIZED_FIELDS]) {
                prefixedMetadata = {
                    ...prefixedMetadata,
                    [LOCALIZED_FIELDS]: {}
                }
            }

            // attribute is not available, prepare empty object
            if (!prefixedMetadata[LOCALIZED_FIELDS][payload.attribute]) {
                prefixedMetadata = {
                    ...prefixedMetadata,
                    [LOCALIZED_FIELDS]: {
                        ...prefixedMetadata[LOCALIZED_FIELDS],
                        [payload.attribute]: {}
                    }
                }
            }

            // merge lang attribute
            prefixedMetadata = {
                ...prefixedMetadata,
                [LOCALIZED_FIELDS]: {
                    ...prefixedMetadata[LOCALIZED_FIELDS],
                    [payload.attribute]: {
                        ...prefixedMetadata[LOCALIZED_FIELDS][payload.attribute],
                        [payload.language]: payload.value
                    }
                }
            }
        } else {
            // merge attribute
            prefixedMetadata = {
                ...prefixedMetadata,
                [payload.attribute]: payload.value
            }
        }

        // merge the value into the state
        return {
            ...state,
            metadataEdit: {
                ...state.metadataEdit,
                [payload.id]: {
                    ...state.metadataEdit[payload.id],
                    metadata: {
                        ...state.metadataEdit[payload.id].metadata,
                        [payload.prefix]: prefixedMetadata
                    }
                }
            }
        }
    },

    [toggleKeepMetadata.type]: (state, {payload}) => {
        if (!state.metadataEdit[payload.id].meta.keep) {
            state.metadataEdit[payload.id].meta.keep = {};
        }

        state.metadataEdit[payload.id].meta.keep[payload.attribute] = !state.metadataEdit[payload.id].meta.keep[payload.attribute];
    },

    [updateDetailTags.type]: (state, {payload}) => {
        state.detail.data.assignedTags = payload;
    },

    ...fetchingStateReducer(detailRequested.type, detailFetched.type, detailFailed.type, "detailFetchingState", "detailError", (state, payload) => {
        state.detail = {
            data: payload.detail
        }
    }),

    ...fetchingStateReducer(metadataLayoutRequested.type, metadataLayoutFetched.type, metadataLayoutFailed.type, "metadataLayoutFetchingState", "metadataLayoutError", (state, payload) => {
        state.metadataLayout = payload;
    }),

    ...fetchingStateReducer(workflowRequested.type, workflowFetched.type, workflowFailed.type, "workflowFetchingState", "metadataLayoutError", (state, payload) => {
        state.workflow = payload.workflow;
        state.statusInfo = payload.statusInfo;
        state.workflowHistory = payload.history;
    }),

    [openWorkflowTransitionModal.type]: (state, {payload}) => {
        state.workflowModalOpen = true;
        state.currentWorkflowTransition = payload;
    },

    [closeWorkflowTransitionModal.type]: (state) => {
        state.workflowModalOpen = false;
        state.currentWorkflowTransition = null;
    },

    [openWorkflowHistoryModal.type]: (state) => {
        state.workflowHistoryModalOpen = true;
    },
    [closeWorkflowHistoryModal.type]: (state) => {

        state.workflowHistoryModalOpen = false;
    },

    ...fetchingStateReducer(versionHistoryRequested.type, versionHistoryFetched.type, versionHistoryFailed.type, "versionHistoryFetchingState", "versionHistoryError", (state, payload) => {
        state.versionHistory = payload;
    }),

    ...fetchingStateReducer(versionComparisonRequested.type, versionComparisonFetched.type, versionComparisonFailed.type, "versionComparisonFetchingState", "versionComparisonError", function (state, payload) {
        state.versionComparison = payload;
    }),

    [toggleVersionSelection.type]: (state, {payload: {id, isSelected}}) => {
        if (isSelected) {
            state.selectedVersionIds.push(id);
        } else {
            state.selectedVersionIds = state.selectedVersionIds.filter(currentId => currentId !== id);
        }
    },

    [editMetaDataClicked]: (state, {payload: {dataPoolId, ids}}) => {
        state.editMetaDataModalOpen = true;
        state.editMetaDataModalIds = ids;
        state.editMetaModalDataDataPoolId = dataPoolId;
    },

    [closeEditMetaDataModal]: state => {
        state.editMetaDataModalOpen = false;
    },

    [BATCH_META_DATA_TYPE.SUCCEEDED]: (state, {payload}) => {
        state.editMetaDataModalOpen = false;
    },

    [workflowUncollapse]: (state) => {
        state.workflowPanelCollapsed = false;
    },

    [updateDirectEditStatus.type]: (state, {payload}) => ({
        ...state,
        directEditStatus: payload.status,
        directEditMessage: payload.message
    })
});