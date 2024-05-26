/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState} from "react";
import {connect} from "react-redux";
import {Modal, Button} from "react-bootstrap";
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import Trans from "~portal-engine/scripts/components/Trans";
import FormGroup from "~portal-engine/scripts/components/FormGroup";
import {
    getCurrentWorkflowTransition,
    getWorkflowModalOpen
} from "~portal-engine/scripts/features/asset/asset-selectors";
import {closeWorkflowTransitionModal, applyWorkflowTransition} from "~portal-engine/scripts/features/asset/asset-actions";

export const mapStateToProps = (state) => ({
    modalOpen: getWorkflowModalOpen(state),
    currentWorkflowTransition: getCurrentWorkflowTransition(state)
});

export const mapDispatchToProps = (dispatch) => ({
    closeModal: () => dispatch(closeWorkflowTransitionModal()),
    applyWorkflowTransition: (payload) => dispatch(applyWorkflowTransition(payload))
});

export function WorkflowModal(props) {
    const {
        modalOpen,
        currentWorkflowTransition,
        closeModal,
        applyWorkflowTransition
    } = props;

    const [comment, setComment] = useState("");
    let valid = true;

    if(currentWorkflowTransition && currentWorkflowTransition.transition.notes.commentRequired) {
        valid = !!comment;
    }

    return (
        <Modal
            show={modalOpen}
            onHide={closeModal}
            centered
        >
            <Modal.Header>
                <Modal.Title>
                    <Trans t="workflow-transition-informations" domain="asset"/>
                </Modal.Title>

                <button type="button" className="close" data-dismiss="modal" onClick={closeModal}>
                    <span aria-hidden="true">
                        <CloseIcon width="22" height="22"/>
                    </span>
                </button>
            </Modal.Header>

            <Modal.Body>
                <FormGroup label={<Trans t="workflow-comment" domain="asset"/>}>
                    <textarea
                        name="comment" className="form-control"
                        value={comment}
                        onChange={(event) => setComment(event.target.value)}
                        rows={9}
                    >
                    </textarea>
                </FormGroup>
            </Modal.Body>

            <Modal.Footer className="justify-content-center">
                <Button
                    disabled={!valid}
                    variant="primary"
                    type="button"
                    className="btn-rounded"
                    onClick={() => {
                        applyWorkflowTransition({
                            ...currentWorkflowTransition,
                            data: {
                                comment: comment
                            }
                        });
                        closeModal();
                    }}
                >
                    <Trans t="apply-transition" domain="asset"/>
                </Button>
            </Modal.Footer>
        </Modal>
    );
}

export default connect(mapStateToProps, mapDispatchToProps)(WorkflowModal);