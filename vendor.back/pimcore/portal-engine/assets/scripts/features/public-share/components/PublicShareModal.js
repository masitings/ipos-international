/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState, Fragment, useEffect, useCallback} from 'react';
import {Modal} from "react-bootstrap";
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import {noop} from "~portal-engine/scripts/utils/utils";
import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import DownloadAttributeList
    from "~portal-engine/scripts/features/download/components/DownloadConfig/DownloadAttributeList";
import {Nav, Tab} from "react-bootstrap";
import {ReactComponent as ShareIcon} from "~portal-engine/icons/share-alt";
import Checkbox from "~portal-engine/scripts/components/Checkbox";
import {connect} from "react-redux";
import {requestDownloadTypes} from "~portal-engine/scripts/features/download/download-actions";
import {getDownloadConfigFetchStateByDataPoolId} from "~portal-engine/scripts/features/selectors";
import {FAILED, FETCHING, NOT_ASKED, SUCCESS,} from "~portal-engine/scripts/consts/fetchingStates";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import Overlay from "~portal-engine/scripts/components/Overlay";
import FormControl from 'react-bootstrap/FormControl'
import InputGroup from 'react-bootstrap/InputGroup'
import Button from "react-bootstrap/Button";
import copy from 'copy-to-clipboard';
import {showNotification} from "~portal-engine/scripts/features/notifications/notifications-actions";
import * as NOTIFICATION_TYPES from "~portal-engine/scripts/consts/notification-types";
import {ReactComponent as WarningIcon} from "~portal-engine/icons/exclamation-circle";

const defaultSelectionState = {
    selectedIds: [],
    formatsById: {},
    setupsById: {}
};

export function PublicShareModal(props) {
    const {
        isLoading = false,
        dataPools = [],
        downloadSelectionStateByDataPoolId = {},
        name = '',
        expiryDate = '',
        termsText = '',
        showTermsText = false,
        shareUrl = '',
        isOpen = false,

        onClose = noop,
        dispatch,

        DesktopComponent = DesktopPublicShare,
        MobileComponent = MobilePublicShare,
    } = props;

    const [currentDataPoolId, setCurrentDataPoolId] = useState(dataPools[0] ? dataPools[0].id : null);
    const [selectionByDataPoolId, setSelectionByDataPoolId] = useState(downloadSelectionStateByDataPoolId);

    useEffect(() => {
        if (dataPools.length && dataPools[0].id) {
            setCurrentDataPoolId(dataPools[0].id);
        }
    }, [dataPools]);

    //load attributes
    useEffect(() => {
        dataPools.forEach(dataPool => {
            dispatch(requestDownloadTypes({dataPoolId: dataPool.id}));
        });
    }, [dataPools]);

    useEffect(() => {
        setSelectionByDataPoolId(downloadSelectionStateByDataPoolId);
        if (dataPools.length && dataPools[0].id) {
            setCurrentDataPoolId(dataPools[0].id);
        }
    }, [isOpen]);

    const handleDataPoolSelection = (dataPoolId) => {
        setCurrentDataPoolId(dataPoolId);
    };

    const defaultFormData = {
        name: name,
        expiryDate: expiryDate,
        termsText: termsText
    };

    const [formData, setFormData] = useState(defaultFormData);
    const [termsCheckboxValue, setTermsCheckboxValue] = useState(showTermsText);

    const handleInputChange = (evt) => {
        let name = evt.target.name;

        setFormData({
            ...formData,
            [name]: evt.target.value
        });
    };

    const closeHandler = () => {
        setSelectionByDataPoolId({});
        setFormData(defaultFormData);
        onClose();
    };

    const labels = {
        name: useTranslation('public-share.label.name'),
        expiryDate: useTranslation('public-share.label.expiry-date'),
        termsText: useTranslation('public-share.label.terms'),
        showTermsText: useTranslation('public-share.label.show-terms'),
    };

    const isSubmitButtonDisabled = !formData.name || !formData.expiryDate || isLoading;

    const copyClickHandler = useCallback(() => {
        copy(shareUrl);
        showNotification({
            type: NOTIFICATION_TYPES.SUCCESS,
            translation: "public-share.modal.copied"
        });
    }, [shareUrl]);

    const childProps = {
        ...props,
        currentDataPoolId,
        onCurrentDataPoolIdChanged: handleDataPoolSelection,
        labels,
        formData,
        onFormDataChanged: handleInputChange,
        showTerms: termsCheckboxValue,
        onShowTermsChanged: setTermsCheckboxValue,
        selectionByDataPoolId,
        onSelectionByDataPoolIdChanged: setSelectionByDataPoolId,
        isSubmitButtonDisabled,
        onClose: closeHandler,
        onCopy: copyClickHandler
    };

    return (
        <Media queries={{
            small: MD_DOWN,
        }}>
            {matches => (
                matches.small
                    ? <MobileComponent {...childProps}/>
                    : <DesktopComponent {...childProps}/>
            )}
        </Media>
    );
}

export function DesktopPublicShare({
    itemIds,
    collectionId,
    isOpen = false,
    isLoading = false,
    dataPools = [],

    onSubmit = noop,
    onClose = noop,

    onCurrentDataPoolIdChanged = noop,
    onShowTermsChanged,
    labels,
    formData,
    onFormDataChanged = noop,
    showTerms,
    selectionByDataPoolId,
    onSelectionByDataPoolIdChanged,
    currentDataPoolId,
    submitState = NOT_ASKED,
    shareUrl = '',
    onCopy = noop,
    error,
    isSubmitButtonDisabled
}) {
    const closeText = useTranslation('public-share.modal.close');

    return (
        <Modal
            show={isOpen}
            size="lg"
            centered
            onHide={onClose}
        >
            <Modal.Header>
                <Modal.Title><Trans t="public-share.modal.title"/></Modal.Title>
                <button type="button"
                        className="close"
                        data-dismiss="modal"
                        aria-label={closeText}
                        onClick={onClose}>
                    <span aria-hidden="true"><CloseIcon width="22" height="22"/></span>
                </button>
            </Modal.Header>

            <Modal.Body className={`p-0 ${(submitState === SUCCESS || isLoading) ? 'bg-light' : ''}`}>
                {isLoading ? (
                    <LoadingIndicator className="my-4"/>
                ) : (
                    submitState === SUCCESS
                        ? (
                            <div className="row justify-content-center my-4">
                                <div className="col-md-9">
                                    <h4><Trans t="public-share.shared.title"/></h4>
                                    <div className="mb-3">
                                        <p className="mb-0"><Trans t="public-share.shared.message"/></p>
                                    </div>

                                    <InputGroup className="mb-3">
                                        <FormControl value={shareUrl} type="text" readOnly={true}/>

                                        <InputGroup.Append>
                                            <Button onClick={onCopy}
                                                    variant="primary"><Trans t="public-share.shared.copy-cta"/></Button>
                                        </InputGroup.Append>
                                    </InputGroup>
                                </div>
                            </div>
                        ) : (
                            dataPools.length
                                ? (
                                    <Tab.Container defaultActiveKey={dataPools[0].id} onSelect={onCurrentDataPoolIdChanged}>

                                        <Nav variant="tabs" className="nav-tabs--lg nav-tabs--bg">
                                            {dataPools.length > 1 ? (
                                                dataPools.map((dataPool) => (
                                                    <Nav.Item key={dataPool.id}>
                                                        <Nav.Link eventKey={dataPool.id}>{dataPool.name}</Nav.Link>
                                                    </Nav.Item>
                                                ))
                                            ) : null}
                                        </Nav>

                                        <div className="tab-content-container">
                                            <div className="row">
                                                <div className="col-6">
                                                    <div className="vertical-gutter">
                                                        {submitState === FAILED && error ? (
                                                            <Warning error={error}/>
                                                        ) : null}

                                                        <div className="vertical-gutter__item">
                                                            <label className="d-block">
                                                                <span className="sr-only">{labels.name}</span>
                                                                <input type="text"
                                                                       name="name"
                                                                       placeholder={labels.name}
                                                                       className="form-control"
                                                                       value={formData.name}
                                                                       onChange={onFormDataChanged}
                                                                />
                                                            </label>
                                                        </div>
                                                        <div className="vertical-gutter__item">
                                                            <label className="d-block">
                                                                <span className="sr-only">{labels.expiryDate}</span>
                                                                <input type="date"
                                                                       min={new Date().toISOString().split('T')[0]}
                                                                       name="expiryDate"
                                                                       className="form-control"
                                                                       value={formData.expiryDate}
                                                                       onChange={onFormDataChanged}
                                                                />
                                                            </label>
                                                        </div>
                                                        <div className="vertical-gutter__item">
                                                            <Checkbox checked={showTerms}
                                                                      label={labels.showTermsText}
                                                                      onChange={() => onShowTermsChanged(!showTerms)}
                                                            />
                                                        </div>

                                                        <div className="vertical-gutter__item">
                                                            <label className="d-block">
                                                                <span className="sr-only">{labels.termsText}</span>
                                                                <textarea name="termsText"
                                                                          disabled={!showTerms}
                                                                          className="form-control"
                                                                          value={showTerms ? formData.termsText : ''}
                                                                          onChange={onFormDataChanged}/>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="col-6">
                                                    <Tab.Content>
                                                        {dataPools.map((dataPool) => (
                                                            <Tab.Pane key={dataPool.id} eventKey={dataPool.id}>
                                                                <DownloadAttributeList
                                                                    dataPoolId={dataPool.id}
                                                                    selections={{
                                                                        ...defaultSelectionState,
                                                                        ...selectionByDataPoolId[currentDataPoolId]
                                                                    }}
                                                                    onChangeSelections={(selections) => {
                                                                        return onSelectionByDataPoolIdChanged({
                                                                            ...selectionByDataPoolId,
                                                                            [currentDataPoolId]: selections
                                                                        });
                                                                    }}
                                                                    filterAttributeOptions={(attribute, format) => format.id !== 'custom'}
                                                                    allowEmptySelection={true}
                                                                />
                                                            </Tab.Pane>
                                                        ))}
                                                    </Tab.Content>
                                                </div>
                                            </div>
                                        </div>
                                    </Tab.Container>
                                ) : null
                        )
                )}
            </Modal.Body>
            {submitState !== SUCCESS
                ? (
                    <Modal.Footer className="justify-content-center">
                        <ButtonWithIcon variant="primary"
                                        disabled={isSubmitButtonDisabled}
                                        Icon={<ShareIcon width="16" height="16"/>}
                                        onClick={() => {
                                            onSubmit({
                                                name: formData.name,
                                                downloadAttributeSelectionByDataPoolId: selectionByDataPoolId,
                                                expiryDate: new Date(formData.expiryDate).getTime() / 1000,
                                                showTermsText: showTerms,
                                                termsText: formData.termsText,
                                                dataPoolConfigId: currentDataPoolId,
                                                itemIds,
                                                collectionId
                                            });
                                        }}>
                            <Trans t='public-share.modal.cta'/>
                        </ButtonWithIcon>
                    </Modal.Footer>
                ) : null
            }
        </Modal>
    )
}

export function MobilePublicShare({
    itemIds,
    collectionId,
    isOpen = false,
    isLoading = false,
    dataPools = [],

    onSubmit = noop,
    onClose = noop,

    onCurrentDataPoolIdChanged = noop,
    onShowTermsChanged,
    labels,
    formData,
    onFormDataChanged = noop,
    showTerms,
    selectionByDataPoolId,
    onSelectionByDataPoolIdChanged,
    currentDataPoolId,
    submitState = NOT_ASKED,
    shareUrl = '',
    onCopy = noop,
    error,
    isSubmitButtonDisabled
}) {
    return (
        <Overlay
            title={<Trans t="public-share.modal.title"/>}
            isShown={isOpen}
            onClose={onClose}
            className="public-share-overlay"
        >
            {isLoading ? (
                <LoadingIndicator className="my-4"/>
            ) : (
                submitState === SUCCESS
                    ? (
                        <div>
                            <h4><Trans t="public-share.shared.title"/></h4>
                            <div className="mb-3"><p className="mb-0">
                                <Trans t="public-share.shared.message"/></p></div>

                            <InputGroup className="mb-3">
                                <FormControl value={shareUrl} type="text" readOnly={true}/>

                                <InputGroup.Append>
                                    <Button onClick={onCopy}
                                            variant="primary"><Trans t="public-share.shared.copy-cta"/></Button>
                                </InputGroup.Append>
                            </InputGroup>
                        </div>
                    ) : (
                        <Fragment>
                            <div className="vertical-gutter">
                                {submitState === FAILED && error ? (
                                    <div className="vertical-gutter__item">
                                        <Warning error={error}/>
                                    </div>
                                ) : null}

                                <div className="vertical-gutter__item">
                                    <label className="d-block">
                                        <span className="sr-only">{labels.name}</span>
                                        <input type="text"
                                               name="name"
                                               placeholder={labels.name}
                                               className="form-control"
                                               value={formData.name}
                                               onChange={onFormDataChanged}
                                        />
                                    </label>
                                </div>
                                <div className="vertical-gutter__item">
                                    <label className="d-block">
                                        <span className="sr-only">{labels.expiryDate}</span>
                                        <input type="date"
                                               name="expiryDate"
                                               className="form-control"
                                               value={formData.expiryDate}
                                               onChange={onFormDataChanged}
                                        />
                                    </label>
                                </div>
                                <div className="vertical-gutter__item">
                                    <Checkbox checked={showTerms}
                                              label={labels.showTermsText}
                                              onChange={() => onShowTermsChanged(!showTerms)}
                                    />
                                </div>

                                {showTerms ? (
                                    <div className="vertical-gutter__item">
                                        <label className="d-block">
                                            <span className="sr-only">{labels.termsText}</span>
                                            <textarea name="termsText"
                                                      className="form-control"
                                                      value={showTerms ? formData.termsText : ''}
                                                      onChange={onFormDataChanged}/>
                                        </label>
                                    </div>
                                ) : null}

                                <div className="vertical-gutter__item">
                                    {dataPools.length
                                        ? (dataPools.length === 1
                                                ? (
                                                    <Fragment>
                                                        <div className="overflow-hidden"/>
                                                        {/*prevent margin collapse*/}
                                                        <DownloadAttributeList
                                                            dataPoolId={dataPools[0].id}
                                                            selections={{
                                                                ...defaultSelectionState,
                                                                ...selectionByDataPoolId[currentDataPoolId]
                                                            }}
                                                            onChangeSelections={(selections) => {
                                                                return onSelectionByDataPoolIdChanged({
                                                                    ...selectionByDataPoolId,
                                                                    [currentDataPoolId]: selections
                                                                });
                                                            }}
                                                            filterAttributeOptions={(attribute, format) => format.id !== 'custom'}
                                                            allowEmptySelection={true}
                                                        />
                                                    </Fragment>
                                                ) : (
                                                    <Tab.Container defaultActiveKey={dataPools[0].id}
                                                                   onSelect={onCurrentDataPoolIdChanged}>
                                                        <Nav variant="tabs" className="nav-tabs--dark">
                                                            {dataPools.map((dataPool) => (
                                                                <Nav.Item key={dataPool.id}>
                                                                    <Nav.Link eventKey={dataPool.id}>{dataPool.name}</Nav.Link>
                                                                </Nav.Item>
                                                            ))}
                                                        </Nav>

                                                        <Tab.Content>
                                                            {dataPools.map((dataPool) => (
                                                                <Tab.Pane key={dataPool.id} eventKey={dataPool.id}>
                                                                    <DownloadAttributeList
                                                                        dataPoolId={dataPool.id}
                                                                        selections={{
                                                                            ...defaultSelectionState,
                                                                            ...selectionByDataPoolId[currentDataPoolId]
                                                                        }}
                                                                        onChangeSelections={(selections) => {
                                                                            return onSelectionByDataPoolIdChanged({
                                                                                ...selectionByDataPoolId,
                                                                                [currentDataPoolId]: selections
                                                                            });
                                                                        }}
                                                                        filterAttributeOptions={(attribute, format) => format.id !== 'custom'}
                                                                        allowEmptySelection={true}
                                                                    />
                                                                </Tab.Pane>
                                                            ))}
                                                        </Tab.Content>
                                                    </Tab.Container>
                                                )
                                        ) : null}
                                </div>
                            </div>

                            <div className="text-center mt-3">
                                <ButtonWithIcon variant="primary"
                                                disabled={isSubmitButtonDisabled}
                                                Icon={<ShareIcon width="16" height="16"/>}
                                                onClick={() => {
                                                    onSubmit({
                                                        name: formData.name,
                                                        downloadAttributeSelectionByDataPoolId: selectionByDataPoolId,
                                                        expiryDate: new Date(formData.expiryDate).getTime() / 1000,
                                                        showTermsText: showTerms,
                                                        termsText: formData.termsText,
                                                        dataPoolConfigId: currentDataPoolId,
                                                        itemIds,
                                                        collectionId
                                                    });
                                                }}>
                                    <Trans t='public-share.modal.cta'/>
                                </ButtonWithIcon>
                            </div>
                        </Fragment>
                    )
            )}
        </Overlay>
    )
}

export function Warning({error}) {
    const warningText = useTranslation('public-share.modal.warning');

    return (
        <div className="font-weight-bold small vertical-gutter__item">
            <div className="row row-gutter--1">
                <div className="col-auto">
                    <WarningIcon className="icon-in-text mr-1"
                                 height="1rem"
                                 title={warningText}
                                 aria-label={warningText}/>
                </div>
                <div className="col">
                    <Trans t={error}/>
                </div>
            </div>
        </div>
    )
}

export const mapStateToProps = (state, {dataPools = [], isLoading}) => ({
    isLoading: isLoading || dataPools.reduce((isLoading, dataPool) => {
        let fetchingState = getDownloadConfigFetchStateByDataPoolId(state, dataPool.id);
        return isLoading || fetchingState === NOT_ASKED || fetchingState === FETCHING;
    }, false)
});

export default connect(mapStateToProps)(PublicShareModal)
