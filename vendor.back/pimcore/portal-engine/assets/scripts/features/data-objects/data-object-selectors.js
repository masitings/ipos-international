/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


export const getDataObjectState = state => state.dataObject;

export const getDataObjectId = state => getDataObjectState(state).dataObjectId;
export const getDataPoolId = state => getDataObjectState(state).dataPoolId;
export const getVersionsEnabled = state => getDataObjectState(state).versionsEnabled;

export const getDetailFetchingState = state => getDataObjectState(state).detailFetchingState;
export const getDetailError = state => getDataObjectState(state).detailError;
export const getDetail = state => getDataObjectState(state).detail || {};
export const getDetailData = state => getDetail(state).data;
export const getDetailBreadcrumbs = state => getDetail(state).breadcrumbs;
export const getValidLanguages = state => getDataObjectState(state).validLanguages;
export const getPermissions = state => getDataObjectState(state).permissions;

export const getVersionsFetchingState = state => getDataObjectState(state).versionsFetchingState;
export const getVersionsError = state => getDataObjectState(state).versionsError;
export const getVersionHistory = state => getDataObjectState(state).versionHistory;

export const getVersionComparisonFetchingState = state => getDataObjectState(state).versionComparisonFetchingState;
export const getVersionComparisonError = state => getDataObjectState(state).versionComparisonError;
export const getVersionComparison = state => getDataObjectState(state).versionComparison;
export const getSelectedVersionIds = state => getDataObjectState(state).selectedVersionIds;