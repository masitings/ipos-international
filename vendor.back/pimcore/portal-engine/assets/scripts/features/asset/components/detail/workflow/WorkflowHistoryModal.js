/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState, Fragment} from "react";
import {connect} from "react-redux";
import {Modal, Button} from "react-bootstrap";
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import Trans from "~portal-engine/scripts/components/Trans";
import {
    getWorkflowHistory,
    getWorkflowHistoryModalOpen
} from "~portal-engine/scripts/features/asset/asset-selectors";
import {closeWorkflowHistoryModal} from "~portal-engine/scripts/features/asset/asset-actions";
import {getLanguage} from "~portal-engine/scripts/utils/intl";

export const mapStateToProps = (state) => ({
    modalOpen:getWorkflowHistoryModalOpen(state),
    workflowHistory: getWorkflowHistory(state)
});

export const mapDispatchToProps = (dispatch) => ({
    closeModal: () => dispatch(closeWorkflowHistoryModal()),
});

export function WorkflowHistoryModal(props) {
    const {
        modalOpen,
        workflowHistory,
        closeModal,
    } = props;


    return (
        <Modal
            show={modalOpen}
            onHide={closeModal}
            centered
        >
            <Modal.Header>
                <Modal.Title>
                    <Trans t="workflow-history" domain="workflow"/>
                </Modal.Title>

                <button type="button" className="close" data-dismiss="modal" onClick={closeModal}>
                    <span aria-hidden="true">
                        <CloseIcon width="22" height="22"/>
                    </span>
                </button>
            </Modal.Header>

            <Modal.Body>
                {workflowHistory.length ? (
                    <Fragment>
                        <div className="data-table-container">
                            <div className="data-table table-responsive">
                                <table className="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><Trans t="workflow-history.status-update" domain="workflow"/></th>
                                            <th><Trans t="workflow-history.description" domain="workflow"/></th>
                                            <th><Trans t="workflow-history.user" domain="workflow"/></th>
                                            <th><Trans t="workflow-history.date" domain="workflow"/></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    {workflowHistory.map(item => (
                                        <tr key={item.date}>
                                            <td>{item.title}</td>
                                            <td>{item.description}</td>
                                            <td>{item.user}</td>
                                            <td>
                                                {new Date(item.date * 1000).toLocaleDateString(getLanguage())}&nbsp;
                                                {new Date(item.date * 1000).toLocaleTimeString(getLanguage(), {
                                                    hour: '2-digit',
                                                    minute: '2-digit',
                                                })}
                                            </td>
                                        </tr>
                                    ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </Fragment>
                ) : (
                    <Trans t="workflow-history.history-empty" domain="workflow"/>
                    )
                }
            </Modal.Body>


        </Modal>
    );
}

export default connect(mapStateToProps, mapDispatchToProps)(WorkflowHistoryModal);