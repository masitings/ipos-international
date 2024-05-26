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
import {Button, Modal} from "react-bootstrap";
import {
    getSubfolderModalState,
    isSubfolderModalOpen
} from "~portal-engine/scripts/features/subfolder/subfolder-selectors";
import {closeSubfolderModal, updateModalState} from "~portal-engine/scripts/features/subfolder/subfolder-actions";
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import Trans from "~portal-engine/scripts/components/Trans";
import FormGroup from "~portal-engine/scripts/components/FormGroup"
import {createSubfolder, renameSubfolder} from "~portal-engine/scripts/features/subfolder/subfolder-api";
import {getSelectedFolderPath} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";

export const mapStateToProps = (state) => ({
    modalOpen: isSubfolderModalOpen(state),
    modalState: getSubfolderModalState(state),
    currentFolder: getSelectedFolderPath(state)
});

export const mapDispatchToProps = (dispatch) => ({
    onClose: () => dispatch(closeSubfolderModal()),
    update: (state) => dispatch(updateModalState(state))
});

export function SubfolderModal(props) {
    const {
        elementType = null,
        modalOpen = false,
        modalState,
        currentFolder,
        update = () => {
        },
        onClose = () => {
        }
    } = props;

    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    let api = () => {};

    if(modalOpen === "create") {
        api = createSubfolder;
    } else {
        api = renameSubfolder;
    }

    const commit = () => {
        if (!api) {
            return;
        }

        setLoading(true);
        setError(null);
        api(elementType, currentFolder, modalState.name)
            .then(({success, data}) => {
                window.location = data.url;
            })
            .catch((error) => {
                if(typeof error === "string") {
                    setError(error);
                }
            })
            .finally(() => {
                setLoading(false);
            });
    };

    return (
        <Modal
            show={!!modalOpen}
            onHide={onClose}
            centered
        >
            <Modal.Header>
                <Modal.Title><Trans t={`subfolder.${modalOpen}`}/></Modal.Title>
                <button
                    type="button"
                    className="close"
                    data-dismiss="modal"
                    onClick={onClose}
                >
                    <span aria-hidden="true">
                        <CloseIcon width="22" height="22"/>
                    </span>
                </button>
            </Modal.Header>

            <Modal.Body>
                {!!error && (
                    <div className={"alert alert-danger"}>
                        {error}
                    </div>
                )}

                <FormGroup label={<Trans t="subfolder-name"/>}>
                    <input
                        className="form-control"
                        value={modalState?.name ? modalState.name : ""}
                        onChange={(event) => {
                            update({
                                ...modalState,
                                name: event.target.value
                            })
                        }}
                        onKeyDown={(event) => {
                            if(event.key === "Enter") {
                                commit()
                            }
                        }}
                    />
                </FormGroup>
            </Modal.Body>

            <Modal.Footer className={"justify-content-center"}>
                <Button
                    disabled={!modalState.name}
                    variant="primary"
                    type="button"
                    className="btn-rounded"
                    onClick={() => commit()}
                >
                    {loading ? (
                        <LoadingIndicator size="inline" showText={false}/>
                    ) : (
                        <Trans t={`subfolder.${modalOpen}.cta`}/>
                    )}
                </Button>
            </Modal.Footer>
        </Modal>
    );
}

export default connect(mapStateToProps, mapDispatchToProps)(SubfolderModal);