/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


export const isModalOpen = state => state.upload.modalOpen;
export const getCurrentStep = state => state.upload.currentStep;

export const getUploadMode = state => state.upload.uploadMode;
export const getUploadContext = state => state.upload.uploadContext;
export const getUploadType = state => state.upload.uploadType;
export const getUploadFiles = state => state.upload.uploadFiles;
export const getUploadZip = state => state.upload.uploadZip;
export const getUploadUrl = state => state.upload.uploadUrl;

export const getUploadId = state => state.upload.uploadId;
export const getUploadProgress = state => state.upload.progress || [];