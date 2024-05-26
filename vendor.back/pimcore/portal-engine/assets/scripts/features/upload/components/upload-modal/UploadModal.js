/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment, useCallback} from "react";
import {Modal} from "react-bootstrap";
import {noop} from "~portal-engine/scripts/utils/utils";
import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import {connect} from "react-redux";
import {useConfirmModal} from "~portal-engine/scripts/components/modals/ConfirmModal";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import Overlay from "~portal-engine/scripts/components/Overlay";
import {modalClosed} from "~portal-engine/scripts/features/upload/upload-actions";
import {getCurrentStep, isModalOpen} from "~portal-engine/scripts/features/upload/upload-selectors";
import * as STEPS from "~portal-engine/scripts/consts/upload-steps";
import UploadSteps from "~portal-engine/scripts/features/upload/components/upload-modal/UploadSteps";

export function UploadModal({
    isOpen = false,
    onClose = noop,
    currentStep = STEPS.UPLOAD_SELECTION,
}) {
    const closeText = useTranslation('upload.modal.close');
    const uploadTitle = useTranslation('upload.modal.title');

    const handleClose = useCallback(() => {
        if (currentStep === STEPS.UPLOAD) {
            confirm();
        } else {
            onClose();
        }
    }, [currentStep]);


    const {confirm, confirmModal, isOpen: isConfirmModalOpen} = useConfirmModal(onClose, {
        title: <Trans t="upload.cancel-prompt.title"/>,
        message: <Trans t="upload.cancel-prompt.message"/>,
        cancelText: <Trans t="upload.cancel-prompt.cancel"/>,
        confirmText: <Trans t="upload.cancel-prompt.confirm"/>,
    });

    return (<Fragment>
            <Media queries={{
                small: MD_DOWN,
            }}>
                {matches => (
                    matches.small
                        ? (
                            <Overlay title={uploadTitle} isShown={isOpen} onClose={handleClose}>
                                <UploadSteps/>
                            </Overlay>
                        ) : (
                            <Modal
                                show={isOpen}
                                onHide={handleClose}
                                centered
                                backdrop={currentStep === STEPS.UPLOAD ? 'static' : true}
                                style={{visibility: isConfirmModalOpen ? 'hidden' : 'visible'}}>
                                <Modal.Header>
                                    <Modal.Title><Trans t="upload.modal.title"/></Modal.Title>
                                    <button type="button"
                                            className="close"
                                            data-dismiss="modal"
                                            aria-label={closeText}
                                            onClick={handleClose}>
                                        <span aria-hidden="true"><CloseIcon width="22" height="22"/></span>
                                    </button>
                                </Modal.Header>

                                <UploadSteps/>
                            </Modal>
                        )
                )}
            </Media>

            {confirmModal}
        </Fragment>
    );
}

export const mapStateToProps = state => ({
    isOpen: isModalOpen(state),
    currentStep: getCurrentStep(state)
});

export const mapDispatchToProps = {
    onClose: () => modalClosed()
};

export default connect(mapStateToProps, mapDispatchToProps)(UploadModal);