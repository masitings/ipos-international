/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from "react";
import Button from "react-bootstrap/Button";
import UploadSelection from "~portal-engine/scripts/features/upload/components/upload-modal/UploadSelection";
import * as UPLOAD_TYPES from "~portal-engine/scripts/consts/upload-types";
import {isValidHttpUrl, noop} from "~portal-engine/scripts/utils/utils";
import {Modal} from "react-bootstrap";
import Trans from "~portal-engine/scripts/components/Trans";
import {uploadSelectionFinished, metaDataFinished} from "~portal-engine/scripts/features/upload/upload-actions";
import {connect} from "react-redux";
import {
    getUploadMode,
    getUploadFiles,
    getUploadType,
    getUploadUrl,
    getUploadZip
} from "~portal-engine/scripts/features/upload/upload-selectors";
import * as UPLOAD_MODES from "~portal-engine/scripts/consts/upload-modes";
import {getPermissions} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";

export function SelectionStep({
    uploadType = UPLOAD_TYPES.FILE,
    uploadFiles,
    uploadZip,
    uploadUrl,
    onFinished = noop
}) {
    const payloadByType = {
        [UPLOAD_TYPES.FILE]: uploadFiles,
        [UPLOAD_TYPES.URL]: uploadUrl,
        [UPLOAD_TYPES.ZIP]: uploadZip,
    };

    const handleButtonClick = () => {
        onFinished({
            type: uploadType,
            payload: payloadByType[uploadType]
        });
    };

    let isValid;
    if (uploadType === UPLOAD_TYPES.URL) {
        isValid = isValidHttpUrl(uploadUrl);
    } else if (uploadType === UPLOAD_TYPES.ZIP) {
        isValid = !!payloadByType[uploadType];
    } else {
        isValid = !!(payloadByType[uploadType] && payloadByType[uploadType].length)
    }

    return (
        <Fragment>
            <Modal.Body className="p-0">
                <UploadSelection uploadType={uploadType}/>
            </Modal.Body>

            <Modal.Footer className="justify-content-center">
                <Button disabled={!isValid}
                        variant="primary"
                        type="button"
                        className="btn-rounded"
                        onClick={handleButtonClick}>
                    <Trans t="upload.selection-step.next"/>
                </Button>
            </Modal.Footer>
        </Fragment>
    )
}

export const mapStateToProps = state => ({
    permissions: getPermissions(state),
    uploadMode: getUploadMode(state),
    uploadType: getUploadType(state),
    uploadFiles: getUploadFiles(state),
    uploadZip: getUploadZip(state),
    uploadUrl: getUploadUrl(state)
});

export const mapDispatchToProps = {
    uploadSelectionFinished,
    metaDataFinished,
};

const mergeProps = (stateProps, dispatchProps, ownProps) => {
    return {
        ...stateProps,
        ...ownProps,
        onFinished: () => {
            if(stateProps.uploadMode === UPLOAD_MODES.MULTI && stateProps.permissions && stateProps.permissions.edit) {
                dispatchProps.uploadSelectionFinished()
            } else {
                // if single upload mode or no edit permissions, skip metadata step
                dispatchProps.metaDataFinished({})
            }
        }
    };
}

export default connect(mapStateToProps, mapDispatchToProps, mergeProps)(SelectionStep)