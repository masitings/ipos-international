/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {createAction} from "@reduxjs/toolkit";
import React from "react";
import * as UPLOAD_TYPES from "~portal-engine/scripts/consts/upload-types";
import {
    cancelUpload,
    startUpload,
    uploadFiles,
    uploadFromUrl,
    uploadZip,
    replaceFile
} from "~portal-engine/scripts/features/upload/upload-api";
import {getMetadataEditDataById} from "~portal-engine/scripts/features/asset/asset-selectors";
import {
    getUploadMode, getUploadContext,
    getUploadFiles, getUploadId,
    getUploadType,
    getUploadUrl, getUploadZip,
} from "~portal-engine/scripts/features/upload/upload-selectors";
import {getSelectedFolderPath} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import {showError} from "~portal-engine/scripts/utils/general";
import {getConfig} from "~portal-engine/scripts/utils/general";
import * as UPLOAD_MODES from "~portal-engine/scripts/consts/upload-modes"
import {resetDetail} from "~portal-engine/scripts/features/asset/asset-actions";

export const fileDropped = (files) => {
    let isZip = files.length === 1 && files[0].type === "application/x-zip-compressed";

    return isZip
        ? uploadZipChanged(files[0])
        : uploadFilesChanged(files);
};

export const modalOpened = createAction('upload/modal/opened');

export const MODAL_CLOSED = 'upload/modal/closed';
export const modalClosed = () => (dispatch, getState) => {
    const state = getState();

    dispatch({
        type: MODAL_CLOSED
    });

    let currentUploadId = getUploadId(state);

    if (currentUploadId) {
        cancelUpload({uploadId: currentUploadId})
            .catch(showError);
    }

    if (abortUpload && typeof abortUpload === "function") {
        abortUpload();
        abortUpload = null;
    }

    revokeUploadFileObjectURL(state);
    revokeUploadZipObjectURL(state);
};
modalClosed.toString = () => MODAL_CLOSED;


// upload selection
export const uploadTypeChanged = createAction('upload/upload-type/changed', ({uploadType}) => ({payload: {uploadType}}));
const UPLOAD_FILES_CHANGED = 'upload/file-upload/changed';
export const uploadFilesChanged = (files) => (dispatch, getState) => {
    revokeUploadFileObjectURL(getState());

    dispatch({
        type: UPLOAD_FILES_CHANGED,
        payload: {
            files: files.map(transformFile)
        }
    });
};
uploadFilesChanged.toString = () => UPLOAD_FILES_CHANGED;

export const uploadUrlChanged = createAction('upload/url-upload/changed', (url) => ({
    payload: {url}
}));

const UPLOAD_ZIP_CHANGED = 'upload/zip-upload/changed';
export const uploadZipChanged = (file) => (dispatch, getState) => {
    revokeUploadZipObjectURL(getState());

    dispatch({
        type: UPLOAD_ZIP_CHANGED,
        payload: {
            file: file ? transformFile(file): null
        }
    });
};
uploadZipChanged.toString = () => UPLOAD_ZIP_CHANGED;


const transformFile = ({binary, type, ...rest}) => ({
    ...rest,
    type,
    objectUrl: URL.createObjectURL(new Blob([binary], {type}))
});

export const uploadSelectionFinished = createAction('upload/upload-selection/finished');

// file upload
export const uploadFinished = createAction('upload/finished', ({uploadId}) => ({payload: {uploadId}}));
export const uploadStarted = createAction('upload/started', ({uploadId}) => ({payload: {uploadId}}));
export const uploadProgress = createAction('upload/upload-progressed', ({progress}) => ({payload: {progress}}));

export const META_DATA_FINISHED = 'upload/meta-data/finished';
let abortUpload;
export const metaDataFinished = ({dataPoolId = getConfig("currentDataPool.id"), tags = [], filename = null}) =>
    (dispatch, getState) => {
        dispatch({
            type: META_DATA_FINISHED
        });

        const mode = getUploadMode(getState());

        if(mode === UPLOAD_MODES.SINGLE_REPLACE) {
            const files = getUploadFiles(getState());

            if(Array.isArray(files) && files.length) {
                const file = files[0];

                addFileBlob(file).then((file) => {
                    const {response, abort} = replaceFile({
                        id: getUploadContext(getState()).id,
                        dataPoolId: dataPoolId,
                        file: file,
                        onProgress: (progress) => {
                            dispatch(uploadProgress({
                                progress: [progress]
                            }));
                        }
                    });

                    abortUpload = abort;

                    response.then(() => {
                        abortUpload = null;
                        dispatch(modalClosed());
                        dispatch(resetDetail());
                    }).catch(showError);
                });
            }
        } else {
            let {abort: abortUploadStart, response: uploadStartedResponse} = startUpload();
            abortUpload = abortUploadStart;

            uploadStartedResponse
                .catch(showError)
                .then(({data: {uploadId}}) => {
                    dispatch(uploadStarted({
                        uploadId,
                    }));

                    const state = getState();
                    const uploadType = getUploadType(state);
                    const folder = getSelectedFolderPath(state);
                    const metadata = getMetadataEditDataById(state, "upload")

                    let upload;
                    switch (uploadType) {
                        case UPLOAD_TYPES.FILE:
                            Promise.all(getUploadFiles(state).map(addFileBlob)).then(files => {
                                upload = uploadFiles({
                                    folder,
                                    files: files,
                                    onProgressesChanged: (progress) => {
                                        dispatch(uploadProgress({progress}));
                                    },
                                    dataPoolId,
                                    uploadId,
                                    metadata,
                                    tags,
                                    filename
                                });

                                abortUpload = upload.abort;

                                upload.response
                                    .then(() => {
                                        dispatch(uploadFinished({uploadId}));
                                        abortUpload = null;
                                    })
                                    .catch(showError);
                            });

                            break;
                        case UPLOAD_TYPES.ZIP:
                            addFileBlob(getUploadZip(state)).then((file) => {
                                upload = uploadZip({
                                    folder,
                                    file,
                                    onProgressesChanged: (progress) => {
                                        dispatch(uploadProgress({progress}));
                                    },
                                    dataPoolId,
                                    uploadId,
                                    metadata,
                                    tags,
                                    filename
                                });

                                abortUpload = upload.abort;

                                upload.response
                                    .then(() => {
                                        dispatch(uploadFinished({uploadId}));
                                        abortUpload = null;
                                    })
                                    .catch(showError);
                            });

                            break;
                        case UPLOAD_TYPES.URL:
                            let uploadUrl = getUploadUrl(state);
                            dispatch(uploadProgress({progress: [0.2]}));

                            upload = uploadFromUrl({
                                url: uploadUrl,
                                folder,
                                dataPoolId,
                                uploadId,
                                metadata,
                                tags,
                                filename
                            });

                            abortUpload = upload.abort;

                            upload.response
                                .then(() => {
                                    dispatch(uploadProgress({progress: [1]}));

                                    dispatch(uploadFinished({uploadId}));
                                    abortUpload = null;
                                })
                                .catch(showError);

                            break;
                    }
                })
        }
    };
metaDataFinished.toString = () => META_DATA_FINISHED;

function addFileBlob({objectUrl, ...rest}) {
    return fetch(objectUrl)
        .then((response) => response.blob())
        .then(blob => ({
            blob,
            ...rest
        }))
}

function revokeUploadFileObjectURL(state) {
    let files = getUploadFiles(state);
    if (files && files.length) {
        getUploadFiles(state).map(({objectUrl}) => URL.revokeObjectURL(objectUrl));
    }
}

function revokeUploadZipObjectURL(state) {
    let zip = getUploadZip(state);
    if (zip && zip.objectUrl) {
        URL.revokeObjectURL(zip.objectUrl)
    }
}