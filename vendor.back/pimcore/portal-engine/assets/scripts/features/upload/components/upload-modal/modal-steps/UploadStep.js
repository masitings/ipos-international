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
import {Modal, ProgressBar} from "react-bootstrap";
import Trans from "~portal-engine/scripts/components/Trans";
import {connect} from "react-redux";
import {
    getUploadFiles,
    getUploadProgress,
    getUploadType,
    getUploadUrl
} from "~portal-engine/scripts/features/upload/upload-selectors";
import * as UPLOAD_TYPES from "~portal-engine/scripts/consts/upload-types";

export function UploadStep({items}) {
    return (
        <Modal.Body className="bg-light scroll-area modal-body--max-height">
            <h3 className="h4 mb-4">
                <Trans t="upload.upload-step.title"/>
            </h3>

            <div className="overflow-hidden">
                <ol className="list-unstyled vertical-gutter--3">
                    {items.map(({progress, text, translation}, index) => (
                        <li className="vertical-gutter__item" key={index}>
                            <div className="text-truncate" id={`upload-progress-label-${index}`}>
                                {text || <Trans t={translation}/>}
                            </div>
                            <ProgressBar aria-labelledby={`upload-progress-label-${index}`}
                                         now={progress * 100}
                                         animated/>
                        </li>
                    ))}
                </ol>
            </div>
        </Modal.Body>
    )
}

export const mapStateToProps = state => {
    let progress = getUploadProgress(state);
    let type = getUploadType(state);

    switch (type) {
        case UPLOAD_TYPES.URL:
            return ({
                items: [{
                    text: getUploadUrl(state),
                    progress: progress[0]
                }]
            });
        case UPLOAD_TYPES.ZIP:
            return ({
                items: [{
                    translation: 'upload.zip.upload-step-1',
                    progress: progress[0]
                }, {
                    translation: 'upload.zip.upload-step-2',
                    progress: progress[1]
                }]
            });
        case UPLOAD_TYPES.FILE:
            let files = getUploadFiles(state);
            return ({
                items: progress.map((progress, index) => ({
                    text: files[index].path,
                    progress
                }))
            });
    }


    return ({
        items: progress
    });
};

export default connect(mapStateToProps)(UploadStep);