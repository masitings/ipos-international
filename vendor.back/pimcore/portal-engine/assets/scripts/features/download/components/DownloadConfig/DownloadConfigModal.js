/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment, useCallback, useEffect, useState} from 'react';
import {connect} from "react-redux";
import {
    getModalState, getMultiDownloadFetchingState, getCollectionDownloadFetchingState,
    getSelectedAttributeFormatsById,
    getSelectedAttributesById, getPublicShareDownloadFetchingState, getSelectedAttributeSetupsById
} from "~portal-engine/scripts/features/download/download-selectors";
import {
    addToCart, closeDownloadConfigModal, directDownload, collectionDownload,
    requestDownloadTypes, updateCartItem, publicShareDownload,
} from "~portal-engine/scripts/features/download/download-actions";
import {noop} from "~portal-engine/scripts/utils/utils";
import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import {ReactComponent as DownloadIcon} from "~portal-engine/icons/arrow-alt-circle-down";
import {ReactComponent as CartIcon} from "~portal-engine/icons/shopping-bag";
import {ReactComponent as SaveIcon} from "~portal-engine/icons/save";
import Modal from "react-bootstrap/Modal";
import DownloadAttributeList
    from "~portal-engine/scripts/features/download/components/DownloadConfig/DownloadAttributeList";
import {
    getDownloadConfigModalDataPoolId,
    getDownloadAttributeIdsByDataPoolId,
    getDownloadConfigFetchStateByDataPoolId,
    getDownloadConfigModalIds,
    getDownloadConfigModalMode
} from "~portal-engine/scripts/features/selectors";
import {getConfigModalAttributes} from "~portal-engine/scripts/features/download/download-selectors";
import {FETCHING, NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import {MODAL_MODES} from "~portal-engine/scripts/features/download/dowload-consts";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import Overlay from "~portal-engine/scripts/components/Overlay";
import Checkbox from "~portal-engine/scripts/components/Checkbox";
import {
    getShowTermsText,
    getTermsText,
    isPublicShare
} from "~portal-engine/scripts/features/public-share/public-share-selectors";


export function mapStateToProps(state) {
    let dataPoolId = getDownloadConfigModalDataPoolId(state);
    let fetchingState = getDownloadConfigFetchStateByDataPoolId(state, dataPoolId);
    let mode = getDownloadConfigModalMode(state);
    let ids = getDownloadConfigModalIds(state);
    const attributes = getConfigModalAttributes(state.download);

    return {
        currentSelectionState: mode === MODAL_MODES.EDIT
            ? {
                selectedIds: getSelectedAttributesById(state.download, ids[0]),
                formatsById: getSelectedAttributeFormatsById(state.download, ids[0]),
                setupsById: getSelectedAttributeSetupsById(state.download, ids[0])
            }
            : defaultSelectionState,
        mode,
        dataPoolId,
        isOpen: getModalState(state.download),
        ids,
        isLoading: fetchingState === NOT_ASKED || fetchingState === FETCHING
            || (mode === MODAL_MODES.DOWNLOAD && getMultiDownloadFetchingState(state.download) === FETCHING)
            || (mode === MODAL_MODES.DOWNLOAD_COLLECTION && getCollectionDownloadFetchingState(state.download) === FETCHING)
            || (mode === MODAL_MODES.DOWNLOAD_PUBLIC_SHARE && getPublicShareDownloadFetchingState(state.download) === FETCHING),
        attributeIds: attributes && attributes.length ? attributes : getDownloadAttributeIdsByDataPoolId(state, dataPoolId),
        showTermsText: isPublicShare(state) && getShowTermsText(state),
        termsText: getTermsText(state)
    }
}

export const mapDispatchToProps = (dispatch, {}) => {
    return {
        onSubmit: ({mode, ...payload}) => {
            if (mode === MODAL_MODES.ADD) {
                dispatch(addToCart(payload));
            } else if (mode === MODAL_MODES.EDIT) {
                dispatch(updateCartItem(payload));
            } else if (mode === MODAL_MODES.DOWNLOAD) {
                dispatch(directDownload(payload))
            } else if (mode === MODAL_MODES.DOWNLOAD_COLLECTION) {
                dispatch(collectionDownload(payload))
            } else if (mode === MODAL_MODES.DOWNLOAD_PUBLIC_SHARE) {
                dispatch(publicShareDownload(payload))
            }
        },
        onDataPoolChanged: (dataPoolId) => {
            dispatch(requestDownloadTypes({dataPoolId}));
        },
        onClose: () => dispatch(closeDownloadConfigModal())
    };
};

const defaultSelectionState = {
    selectedIds: [],
    formatsById: {},
    setupsById: {}
};

export function DownloadConfigModal(props) {
    // default components
    props = {
        DesktopComponent: DesktopDownloadConfig,
        MobileComponent: MobileDownloadConfig,
        ...props
    };

    const {
        dataPoolId,
        onDataPoolChanged = noop,
    } = props;

    useEffect(() => {
        if (dataPoolId) {
            onDataPoolChanged(dataPoolId);
        }
    }, [dataPoolId]);

    return (
        <Media queries={{
            small: MD_DOWN,
        }}>
            {matches => (
                matches.small
                    ? <MobileDownloadConfig {...props}/>
                    : <DesktopDownloadConfig {...props}/>
            )}
        </Media>
    );
}

export function MobileDownloadConfig(props) {
    const {
        dataPoolId,
        ids = [],
        isOpen,
        mode = MODAL_MODES.EDIT,
        isLoading = false,
        currentSelectionState = defaultSelectionState,
        attributeIds = [],
        showTermsText = false,
        termsText = "",
        children,
        onSubmit = noop,
        onClose = noop,
    } = props;

    const [selectionState, setSelections] = useState(defaultSelectionState);
    const [termsCheckboxValue, setTermsCheckboxValue] = useState(false);

    useEffect(() => {
        setSelections(currentSelectionState);
    }, [currentSelectionState]);

    const closeHandler = useCallback(() => {
        onClose(!isOpen);
    }, []);

    useEffect(() => { // reset form state when closed
        if (!isOpen) {
            setSelections(defaultSelectionState);
            setTermsCheckboxValue(false);
        }
    }, [isOpen]);

    let isSubmitDisabled = isLoading
        || (attributeIds.length > 1 && selectionState.selectedIds.length <= 0)
        || (showTermsText && !termsCheckboxValue);

    return (
        <Overlay title={<Trans t={getTitleTranslationKeyByMode(mode)}/>} isShown={isOpen} onClose={closeHandler}>

            <div className="row justify-content-center">
                <div className="col-md-9">
                    <DownloadAttributeList
                        dataPoolId={dataPoolId}
                        selections={selectionState}
                        onChangeSelections={(selections) => setSelections(selections)}/>

                    {showTermsText
                        ? (
                            <Checkbox className="my-4"
                                      allowHtml={true}
                                      label={termsText}
                                      checked={termsCheckboxValue}
                                      onChange={() => setTermsCheckboxValue(!termsCheckboxValue)}/>
                        )
                        : null
                    }

                    {children}
                </div>
            </div>

            <div className="text-center mt-3">
                <ButtonWithIcon
                    variant="primary"
                    disabled={isSubmitDisabled}
                    Icon={iconsByMode[mode]}
                    onClick={() => onSubmit({
                        mode,
                        dataPoolId,
                        ids,
                        ...selectionState,
                        ...(attributeIds.length === 1 ? {selectedIds: attributeIds} : null)
                    })}>
                    <Trans t={getCTATranslationKeyByMode(mode)}/>
                </ButtonWithIcon>
            </div>
        </Overlay>
    )
}

export function DesktopDownloadConfig(props) {
    const {
        dataPoolId,
        ids = [],
        isOpen,
        mode = MODAL_MODES.EDIT,
        attributeIds = [],
        isLoading = false,
        currentSelectionState = defaultSelectionState,
        showTermsText = false,
        termsText = "",
        children,
        onSubmit = noop,
        onClose = noop,
    } = props;

    let closeText = useTranslation('download.modal.close');

    const [selectionState, setSelections] = useState(defaultSelectionState);
    const [termsCheckboxValue, setTermsCheckboxValue] = useState(false);

    useEffect(() => {
        setSelections(currentSelectionState);
    }, [currentSelectionState]);

    const closeHandler = useCallback(() => {
        onClose(!isOpen);
    }, []);

    useEffect(() => { // reset form state when closed
        if (!isOpen) {
            setSelections(defaultSelectionState);
            setTermsCheckboxValue(false);
        }
    }, [isOpen]);

    let isSubmitDisabled = isLoading
        || (attributeIds.length > 1 && selectionState.selectedIds.length <= 0)
        || (showTermsText && !termsCheckboxValue);

    return (
        <Modal
            show={isOpen}
            centered
            onHide={closeHandler}
        >
            <Modal.Header>
                <Modal.Title>
                    <Trans t={getTitleTranslationKeyByMode(mode)}/>
                </Modal.Title>
                <button type="button"
                        className="close"
                        data-dismiss="modal"
                        aria-label={closeText}
                        onClick={closeHandler}>
                    <span aria-hidden="true"><CloseIcon width="22" height="22"/></span>
                </button>
            </Modal.Header>
            <Fragment>
                <Modal.Body className="bg-light">
                    <div className="row justify-content-center">
                        <div className="col-md-9">
                            <DownloadAttributeList
                                dataPoolId={dataPoolId}
                                selections={selectionState}
                                onChangeSelections={(selections) => setSelections(selections)}/>

                            {showTermsText
                                ? (
                                    <Checkbox className="my-4"
                                              allowHtml={true}
                                              label={termsText}
                                              checked={termsCheckboxValue}
                                              onChange={() => setTermsCheckboxValue(!termsCheckboxValue)}/>
                                )
                                : null
                            }

                            {children}
                        </div>
                    </div>
                </Modal.Body>
                <Modal.Footer className="justify-content-center">
                    <ButtonWithIcon variant="primary"
                                    disabled={isSubmitDisabled}
                                    Icon={iconsByMode[mode]}
                                    onClick={() => onSubmit({
                                        mode,
                                        dataPoolId,
                                        ids,
                                        ...selectionState,
                                        ...(attributeIds.length === 1 ? {selectedIds: attributeIds} : null)
                                    })}>
                        <Trans t={getCTATranslationKeyByMode(mode)}/>
                    </ButtonWithIcon>
                </Modal.Footer>
            </Fragment>
        </Modal>
    )
}

const modeTranslationKeyByMode = {
    [MODAL_MODES.EDIT]: 'edit',
    [MODAL_MODES.ADD]: 'add-to-cart',
    [MODAL_MODES.DOWNLOAD]: 'download',
    [MODAL_MODES.DOWNLOAD_COLLECTION]: 'download-collection',
    [MODAL_MODES.DOWNLOAD_PUBLIC_SHARE]: 'download-public-share',
};
const getTitleTranslationKeyByMode = (mode) => {
    return `download.modal.title.${modeTranslationKeyByMode[mode]}`;
};
const getCTATranslationKeyByMode = (mode) => {
    return `download.modal.cta.${modeTranslationKeyByMode[mode]}`;
};

const iconsByMode = {
    [MODAL_MODES.EDIT]: <SaveIcon width="17" height="17"/>,
    [MODAL_MODES.ADD]: <CartIcon width="17" height="17"/>,
    [MODAL_MODES.DOWNLOAD]: <DownloadIcon width="17" height="17"/>,
    [MODAL_MODES.DOWNLOAD_COLLECTION]: <DownloadIcon width="17" height="17"/>,
    [MODAL_MODES.DOWNLOAD_PUBLIC_SHARE]: <DownloadIcon width="17" height="17"/>,
};

export default connect(mapStateToProps, mapDispatchToProps)(DownloadConfigModal);