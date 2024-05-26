/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import Trans from "~portal-engine/scripts/components/Trans";
import React, {useCallback, Fragment} from "react";
import * as UPLOAD_TYPES from "~portal-engine/scripts/consts/upload-types";
import {noop} from "~portal-engine/scripts/utils/utils";
import { Nav, Tab} from "react-bootstrap";
import FileUpload from "~portal-engine/scripts/features/upload/components/upload-modal/FileUplaod";
import UrlUpload from "~portal-engine/scripts/features/upload/components/upload-modal/UrlUpload";
import ZipUpload from "~portal-engine/scripts/features/upload/components/upload-modal/ZipUpload";
import {
    getUploadMode,
    getUploadFiles,
    getUploadType,
    getUploadUrl,
    getUploadZip
} from "~portal-engine/scripts/features/upload/upload-selectors";
import {
    uploadFilesChanged,
    uploadTypeChanged,
    uploadUrlChanged,
    uploadZipChanged
} from "~portal-engine/scripts/features/upload/upload-actions";
import {connect} from "react-redux";
import * as UPLOAD_MODES from "~portal-engine/scripts/consts/upload-modes";

export function UploadSelection({
    uploadMode,
    uploadFiles,
    uploadZip,
    uploadUrl,
    onFilesChange,
    onUrlChange,
    onZipChange,
    uploadType = UPLOAD_TYPES.FILE,
    onUploadTypeChange = noop,
}) {
    const handleTypeSelection = useCallback((uploadType) => onUploadTypeChange({uploadType}));
    const handleZipChange = useCallback(files => onZipChange(files[0]));

    return (
        <Tab.Container activeKey={uploadType} onSelect={handleTypeSelection}>
            <Nav variant="tabs" className="nav-tabs--lg nav-tabs--bg">
                <Nav.Item>
                    <Nav.Link eventKey={UPLOAD_TYPES.FILE}><Trans t="upload.files.tab-title"/></Nav.Link>
                </Nav.Item>
                {uploadMode === UPLOAD_MODES.MULTI && (
                    <Fragment>
                        <Nav.Item>
                            <Nav.Link eventKey={UPLOAD_TYPES.URL}><Trans t="upload.url.tab-title"/></Nav.Link>
                        </Nav.Item>
                        <Nav.Item>
                            <Nav.Link eventKey={UPLOAD_TYPES.ZIP}><Trans t="upload.zip.tab-title"/></Nav.Link>
                        </Nav.Item>
                    </Fragment>
                )}
            </Nav>

            <Tab.Content className={"scroll-area modal-body--max-height"}>
                <Tab.Pane eventKey={UPLOAD_TYPES.FILE}>
                    <FileUpload onFilesChanged={onFilesChange} files={uploadFiles} multi={uploadMode === UPLOAD_MODES.MULTI}/>
                </Tab.Pane>
                {uploadMode === UPLOAD_MODES.MULTI && (
                    <Fragment>
                        <Tab.Pane eventKey={UPLOAD_TYPES.URL}>
                            <UrlUpload onChange={onUrlChange} value={uploadUrl}/>
                        </Tab.Pane>
                        <Tab.Pane eventKey={UPLOAD_TYPES.ZIP}>
                            <ZipUpload onFilesChanged={handleZipChange} files={uploadZip ? [uploadZip]: []}/>
                        </Tab.Pane>
                    </Fragment>
                )}
            </Tab.Content>
        </Tab.Container>
    )
}

export const mapStateToProps = state => ({
    uploadMode: getUploadMode(state),
    uploadType: getUploadType(state),
    uploadFiles: getUploadFiles(state, UPLOAD_TYPES.FILE),
    uploadZip: getUploadZip(state, UPLOAD_TYPES.ZIP),
    uploadUrl: getUploadUrl(state, UPLOAD_TYPES.URL)
});

export const mapDispatchToProps = {
    onUploadTypeChange: uploadTypeChanged,
    onFilesChange: uploadFilesChanged,
    onUrlChange: uploadUrlChanged,
    onZipChange: uploadZipChanged,
};

export default connect(mapStateToProps, mapDispatchToProps)(UploadSelection)