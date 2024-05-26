/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState} from 'react';
import {connect} from "react-redux";
import {Modal} from "react-bootstrap";
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import {noop} from "~portal-engine/scripts/utils/utils";
import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import {getUpdateModalState, getUpdateModalIds, getUpdateItemModalDataPoolId, getUpdateItemModalLoading} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import {closeRelocateModal, relocateItems} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {getSelectedFolderPath} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import FolderTreeRelocate from "~portal-engine/scripts/features/folders/components/FolderTreeRelocate";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import {ReactComponent as SaveIcon} from "~portal-engine/icons/save";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import Overlay from "~portal-engine/scripts/components/Overlay";

export function UpdateModal({
    dataPoolId,
    ids,
    fromDetailPage = false,
    isOpen = false,
    isLoading = false,
    isModalLoading = false,
    onSubmit = noop,
    onClose = noop,
    parentPath = null,
    currentPath = null,
    onSelectionChange = noop,
    submitCallback = noop
}) {
    const closeText = useTranslation('data-pool.update-modal.close');
    const title = useTranslation('data-pool.relocate-modal.title');
    const [folder, setFolder] = useState(currentPath);

    const onSelectFolder = (newPath) => {
        setFolder(newPath.path);
    };

    const handleClose = () => {
        onClose();
        setFolder(null);
    };

    return (
        <Media queries={{
            small: MD_DOWN,
        }}>
            {matches => (
                matches.small
                    ? (
                        <Overlay title={title} isShown={isOpen} onClose={() => handleClose()}>
                            {isLoading || isModalLoading ? (
                                <LoadingIndicator className="my-4"/>
                            ) : (
                                <FolderTreeRelocate parentPath={parentPath} selectedPath={folder} onSelectionChange={(newPath) => onSelectFolder(newPath)} />
                            )}

                            <div className="text-center mt-3">
                                <ButtonWithIcon variant="primary"
                                                disabled={!folder}
                                                Icon={<SaveIcon width="16" height="16"/>}
                                                onClick={() => onSubmit({
                                                    ids,
                                                    folder,
                                                    dataPoolId
                                                })}>
                                    <Trans t='data-pool.relocate-modal.cta'/>
                                </ButtonWithIcon>
                            </div>
                        </Overlay>
                    ) : (
                        <Modal
                            show={isOpen}
                            onHide={() => handleClose()}
                            centered
                        >
                            <Modal.Header>
                                <Modal.Title>
                                    <Trans t='data-pool.relocate-modal.title'/>
                                </Modal.Title>
                                <button type="button"
                                        className="close"
                                        data-dismiss="modal"
                                        aria-label={closeText}
                                        onClick={() => handleClose()}>
                                    <span aria-hidden="true"><CloseIcon width="22" height="22"/></span>
                                </button>
                            </Modal.Header>
                            <Modal.Body>
                                {isLoading || isModalLoading ? (
                                    <LoadingIndicator className="my-4"/>
                                ) : (
                                    <FolderTreeRelocate parentPath={parentPath} selectedPath={folder} onSelectionChange={(newPath) => onSelectFolder(newPath)} />
                                )}
                            </Modal.Body>
                            <Modal.Footer className="justify-content-center">
                                <ButtonWithIcon variant="primary"
                                                disabled={!folder}
                                                Icon={<SaveIcon width="16" height="16"/>}
                                                onClick={() => onSubmit({
                                                    ids,
                                                    folder,
                                                    dataPoolId,
                                                    fromDetailPage
                                                })}>
                                    <Trans t='data-pool.relocate-modal.cta'/>
                                </ButtonWithIcon>
                            </Modal.Footer>
                        </Modal>
                    )
            )}
        </Media>
    )
}

export function mapStateToProps(state) {
    let ids = getUpdateModalIds(state);

    return {
        ids,
        isModalLoading: getUpdateItemModalLoading(state),
        dataPoolId: getUpdateItemModalDataPoolId(state),
        isOpen: getUpdateModalState(state),
        selectedPath: getSelectedFolderPath(state)
    };
}

export const mapDispatchToProps = (dispatch, {submitCallback=noop}) => {
    return {
        onSubmit: ({...payload}) => dispatch(relocateItems(payload)).then(submitCallback),
        onClose: () => dispatch(closeRelocateModal())
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(UpdateModal);