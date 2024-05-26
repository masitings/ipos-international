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
import {
    metaDataFinished,
    modalClosed,
    modalOpened, uploadFilesChanged, uploadFinished, uploadProgress, uploadSelectionFinished, uploadStarted,
    uploadTypeChanged, uploadUrlChanged, uploadZipChanged
} from "~portal-engine/scripts/features/upload/upload-actions";
import * as STEPS from "~portal-engine/scripts/consts/upload-steps";
import * as UPLOAD_TYPES from "~portal-engine/scripts/consts/upload-types";
import * as UPLOAD_MODES from "~portal-engine/scripts/consts/upload-modes";

const initialState = {
    modalOpen: false,
    currentStep: STEPS.UPLOAD_SELECTION,

    uploadMode: UPLOAD_MODES.MULTI,
    uploadContext: {},
    uploadType: UPLOAD_TYPES.FILE,
    uploadFiles: [],
    uploadZip: null,
    uploadUrl: "",

    uploadId: null,
    progress: []
};

export default createReducer(initialState, {
    [modalOpened]: (state, {payload = {}}) => {
        state.modalOpen = true;
        state.currentStep = STEPS.UPLOAD_SELECTION;
        state.uploadMode = payload.mode || UPLOAD_MODES.MULTI;
        state.uploadContext = payload.context || {};
    },
    [modalClosed]: (state) => {
        state.modalOpen = false;
        state.uploadMode = UPLOAD_MODES.MULTI;
        state.uploadContext = {};
        state.uploadType = UPLOAD_TYPES.FILE;
        state.uploadFiles = [];
        state.uploadZip = null;
        state.uploadUrl = "";
        state.uploadId = null;
        state.progress = [];
    },
    [uploadTypeChanged]: (state, {payload: {uploadType}}) => {
        state.uploadType = uploadType;
    },
    [uploadFilesChanged]: (state, {payload: {files}}) => {
        state.modalOpen = true;
        state.currentStep = STEPS.UPLOAD_SELECTION;
        state.uploadType = UPLOAD_TYPES.FILE;
        state.uploadFiles = files;
    },
    [uploadUrlChanged]: (state, {payload: {url}}) => {
        state.uploadUrl = url;
    },
    [uploadZipChanged]: (state, {payload: {file}}) => {
        state.modalOpen = true;
        state.currentStep = STEPS.UPLOAD_SELECTION;
        state.uploadType = UPLOAD_TYPES.ZIP;
        state.uploadZip = file;
    },
    [uploadSelectionFinished]: (state) => {
        state.currentStep = STEPS.META_DATA;
    },
    [metaDataFinished]: (state) => {
        state.currentStep = STEPS.UPLOAD;

        switch (state.uploadType) {
            case UPLOAD_TYPES.URL:
                state.progress = [0.1];
                break;
            case UPLOAD_TYPES.ZIP:
                state.progress = [0, 0];
                break;
            case UPLOAD_TYPES.FILE:
                state.progress = new Array(state.uploadFiles.length).fill(0);
                break;
        }
    },

    [uploadStarted]: (state, {payload: {uploadId}}) => {
        state.uploadId = uploadId;
    },
    [uploadProgress]: (state, {payload: {progress = []}}) => {
        state.progress = progress;
    },
    [uploadFinished]: (state) => {
        state.currentStep = STEPS.FINISHED;
    },
});