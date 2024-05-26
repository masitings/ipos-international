/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {getDataObjectState} from "../data-objects/data-object-selectors";

export const getAssetState = state => state.asset;

export const getAssetId = state => getAssetState(state).assetId;
export const getDetailFetchingState = state => getAssetState(state).detailFetchingState;
export const getDetail = state => getAssetState(state).detail;
export const getDetailData = state => getDetail(state).data;
export const getPermissions = state => getDetailData(state).permissions;
export const getDetailMetadata = state => getDetailData(state).metadata;
export const getDetailError = state => getAssetState(state).detailError;

export const getMetadataLayoutFetchingState = state => getAssetState(state).metadataLayoutFetchingState;
export const getMetadataLayoutError = state => getAssetState(state).metadataLayoutError;
export const getMetadataLayout = state => getAssetState(state).metadataLayout;
export const getMetadataEditData = state => getAssetState(state).metadataEdit;
export const getMetadataEditDataById = (state, id) => getMetadataEditData(state)[id] || null;
export const hasMetadataEditById = (state, id) => !!getMetadataEditDataById(state, id);
export const getMetadataEditMetadataById = (state, id) => getMetadataEditDataById(state, id).metadata;
export const getMetadataEditRemovedById = (state, id) => getMetadataEditDataById(state, id).removed;
export const getMetadataEditMetaById = (state, id) => getMetadataEditDataById(state, id).meta;
export const getMetadataEditValidationById = (state, id) => getMetadataEditDataById(state, id).validation;

export const getEditMetaDataModalState = state => (state.asset.editMetaDataModalOpen);
export const getEditMetaDataModalIds = (state) => state.asset.editMetaDataModalIds;
export const getEditMetaDataModalDataPoolId = (state) => state.asset.editMetaModalDataDataPoolId;

export const getWorkflowData = state => state.asset.workflow;
export const getWorkflowStatusInfo = state => state.asset.statusInfo;
export const getWorkflowHistory = state => state.asset.workflowHistory;
export const getWorkflowFetchingState = state => state.asset.workflowFetchingState;
export const getWorkflowModalOpen = state => getAssetState(state).workflowModalOpen;
export const getWorkflowHistoryModalOpen = state => getAssetState(state).workflowHistoryModalOpen;
export const getWorkflowPanelCollapsed = state => getAssetState(state).workflowPanelCollapsed;
export const getCurrentWorkflowTransition = state => getAssetState(state).currentWorkflowTransition;

export const getVersionHistoryFetchingState = state => getAssetState(state).versionHistoryFetchingState;
export const getVersionHistory = state => getAssetState(state).versionHistory;

export const getVersionComparisonFetchingState = state => getAssetState(state).versionComparisonFetchingState;
export const getVersionComparisonError = state => getAssetState(state).versionComparisonError;
export const getVersionComparison = state => getAssetState(state).versionComparison;
export const getSelectedVersionIds = state => getAssetState(state).selectedVersionIds;

export const getDirectEditStatus = state => getAssetState(state).directEditStatus;
export const getDirectEditMessage = state => getAssetState(state).directEditMessage;