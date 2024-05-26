/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import React, {useEffect, useState} from "react";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import {ReactComponent as DocumentsIcon} from "~portal-engine/icons/documents";
import {noop} from "~portal-engine/scripts/utils/utils";
import ReactSelect from 'react-select/creatable';
import Modal from "react-bootstrap/Modal";
import {connect} from "react-redux";
import {
    getAddToModalDataPoolId,
    getAddToModalIds,
    getAddToModalState, getAddToRequestState
} from "~portal-engine/scripts/features/collections/collections-selectors";
import {
    addedToCollection,
    addedToNewCollection,
    closeAddToModal,
} from "~portal-engine/scripts/features/collections/collections-actions";
import {FETCHING} from "~portal-engine/scripts/consts/fetchingStates";
import {getAllEditableCollections} from "~portal-engine/scripts/features/collections/collections-api";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import Overlay from "~portal-engine/scripts/components/Overlay";

export function AddToCollectionModal({
    isOpen = false,
    isAdding = false,
    dataPoolId,
    selectedIds,
    onAddTo = noop,
    onAddToNew = noop,
    onClose = noop,
    // onMount = noop,
}) {
    let closeText = useTranslation('collection.add-modal.close');
    const [isLoading, setLoading] = useState(false);
    const [options, setOptions] = useState([]);

    const handleChange = (option) => {
        setSelectedOption(option);
    };

    const onInternalClose = () => {
        onClose();
        setSelectedOption(null);
    };

    useEffect(() => {
        let abortFnc = noop;

        if (isOpen && !isLoading) {
            const {response, abort} = getAllEditableCollections();
            abortFnc = abort;
            setLoading(true);

            response.then(({data}) => {
                setLoading(false);
                setOptions(data.map(({id, name}) => ({
                    value: id,
                    label: name
                })));

                abortFnc = noop;
            });
        }

        // clean up
        return abortFnc;
    }, [isOpen]);

    const [selectedOption, setSelectedOption] = useState(null);
    const translation = useTranslation('collection.add-modal.create-new');
    const title = useTranslation('collection.add-modal.title');

    return (
        <Media queries={{
            small: MD_DOWN,
        }}>
            {matches => (
                matches.small
                    ? (
                        <Overlay title={title} isShown={isOpen} onClose={() => onInternalClose()}>
                            {isLoading || isAdding ? (
                                <LoadingIndicator className="my-4"/>
                            ) : (
                                <div className="row justify-content-center">
                                    <div className="col-md-9">
                                        <label className="form-group d-block">
                                            <Trans t="collection.add-modal.label"/>

                                            <ReactSelect
                                                className="react-select"
                                                classNamePrefix={`react-select`}
                                                formatCreateLabel={(text) => getCreatOptionText(translation, text)}
                                                isClearable
                                                onChange={handleChange}
                                                options={options}
                                                value={selectedOption}
                                            />
                                        </label>
                                    </div>
                                </div>
                            )}

                            <div className="text-center">
                                <button type="button"
                                        className="btn btn-primary btn-rounded btn-with-addon"
                                        disabled={isLoading || isAdding || !selectedOption}
                                        onClick={() => {
                                            selectedOption.__isNew__
                                                ? onAddToNew({
                                                    dataPoolId,
                                                    selectedIds,
                                                    collectionName: selectedOption.value
                                                })
                                                : onAddTo({
                                                    dataPoolId,
                                                    selectedIds,
                                                    collectionId: selectedOption.value
                                                });

                                            setSelectedOption(null);
                                        }}>
                            <span className="btn__addon">
                                <DocumentsIcon width="18" height="18"/>
                            </span>

                                    <Trans t="collection.add-modal.cta"/>
                                </button>
                            </div>
                        </Overlay>
                    ) : (
                        <Modal
                            show={isOpen}
                            backdrop="static"
                            centered
                        >
                            <Modal.Header>
                                <Modal.Title>
                                    <Trans t="collection.add-modal.title"/>
                                </Modal.Title>
                                <button type="button"
                                        className="close"
                                        data-dismiss="modal"
                                        aria-label={closeText}
                                        onClick={() => onInternalClose()}>
                                    <span aria-hidden="true"><CloseIcon width="22" height="22"/></span>
                                </button>
                            </Modal.Header>
                            <Modal.Body className="bg-light">
                                {isLoading || isAdding ? (
                                    <LoadingIndicator className="my-4"/>
                                ) : (
                                    <div className="row justify-content-center">
                                        <div className="col-md-9">
                                            <label className="form-group d-block">
                                                <Trans t="collection.add-modal.label"/>

                                                <ReactSelect
                                                    formatCreateLabel={(text) => getCreatOptionText(translation, text)}
                                                    isClearable
                                                    onChange={handleChange}
                                                    options={options}
                                                    value={selectedOption}
                                                />
                                            </label>
                                        </div>
                                    </div>
                                )}
                            </Modal.Body>
                            <Modal.Footer className="justify-content-center">
                                <button type="button"
                                        className="btn btn-primary btn-rounded btn-with-addon"
                                        disabled={isLoading || isAdding || !selectedOption}
                                        onClick={() => {
                                            selectedOption.__isNew__
                                                ? onAddToNew({
                                                    dataPoolId,
                                                    selectedIds,
                                                    collectionName: selectedOption.value
                                                })
                                                : onAddTo({
                                                    dataPoolId,
                                                    selectedIds,
                                                    collectionId: selectedOption.value
                                                });

                                            setSelectedOption(null);
                                        }}>
                            <span className="btn__addon">
                                <DocumentsIcon width="18" height="18"/>
                            </span>

                                    <Trans t="collection.add-modal.cta"/>
                                </button>
                            </Modal.Footer>
                        </Modal>
                    )
            )}
        </Media>
    )
}

function getCreatOptionText(translation, value) {
    if (translation && translation.replace) {
        translation = translation.replace('[name]', value);
    }

    return translation;
}

export const mapStateToProps = (state) => ({
    isOpen: getAddToModalState(state),
    isAdding: getAddToRequestState(state) === FETCHING,
    selectedIds: getAddToModalIds(state),
    dataPoolId: getAddToModalDataPoolId(state),
});

export const mapDispatchToProps = {
    onClose: closeAddToModal,
    onAddTo: addedToCollection,
    onAddToNew: addedToNewCollection
};

export default connect(mapStateToProps, mapDispatchToProps)(AddToCollectionModal);