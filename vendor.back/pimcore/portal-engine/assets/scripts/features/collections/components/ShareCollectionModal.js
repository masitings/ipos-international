/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState, Fragment, useEffect} from 'react';
import {connect} from "react-redux";
import {Modal} from "react-bootstrap";
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import {ReactComponent as TrashIcon} from "~portal-engine/icons/trash-alt";
import {noop} from "~portal-engine/scripts/utils/utils";
import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import {getShareSmartSuggest} from "~portal-engine/scripts/features/collections/collections-api";
import AsyncSelect from "react-select/async";
import {debounce} from 'throttle-debounce';
import {EDIT, READ} from "~portal-engine/scripts/consts/permissions";
import Radio from "~portal-engine/scripts/components/Radio";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import {ReactComponent as ShareIcon} from "~portal-engine/icons/share-alt";
import {
    requestCollectionShareList,
    updateCollectionShareList
} from "~portal-engine/scripts/features/collections/collections-actions";
import {FETCHING, NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import {
    getShareListByCollectionId,
    getShareListRequestStateByCollectionId
} from "~portal-engine/scripts/features/collections/collections-selectors";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";


const permissionTypes = [{
    id: READ,
    translationKey: 'collection.share-modal.permission.read'
}, {
    id: EDIT,
    translationKey: 'collection.share-modal.permission.edit'
}];

export function ShareModal({
    shareList,
    isOpen = false,
    shareListFetchingState = NOT_ASKED,
    isLoading = false,
    onRequestData = noop,
    onSubmit = noop,
    onClose = noop,
}) {
    const [permissions, setPermissions] = useState(shareList);

    useEffect(() => {
        setPermissions(shareList);
    }, [shareList]);

    // load data as soon as the modal is opened
    useEffect(() => {
        if (isOpen && shareListFetchingState === NOT_ASKED) {
            onRequestData();
        }
    }, [isOpen]);


    // select
    const getAsyncOptions = inputValue => {
        if (!inputValue || inputValue === '') {
            return Promise.resolve([]);
        }

        return new Promise(resolve => {
            debouncedSmartSuggest(function ({success, data}) {
                if (success) {
                    resolve(
                        data.map(({id, name, type}) => ({label: name, value: id, type}))
                        // .filter(isOptionAlreadyUsed)
                    );
                }
            }, {text: inputValue});
        });
    };

    const addPermissionRowHandler = ({value, label, type}) => {
        setPermissions([
            ...permissions, {
                id: value,
                name: label,
                type,
                permission: READ
            }
        ]);
    };

    const isOptionAlreadyUsed = option => !permissions.some(permissionEntry => permissionEntry.id === option.value);


    // Radio
    const handleRadioChange = (id, permission) => {
        setPermissions(permissions.map((item) => item.id === id
            ? {
                ...item,
                permission,
            } : item))
    };

    // Remove item
    const removeItem = (itemToRemove) => {
        setPermissions(
            permissions.filter((permissionItem) => permissionItem !== itemToRemove)
        )
    };

    const closeText = useTranslation('collection.share-modal.close');

    const handleClose = () => {
        onClose();
        setPermissions(shareList);
    };

    return (
        <Modal
            show={isOpen}
            onHide={() => handleClose()}
            centered
        >
            <Modal.Header>
                <Modal.Title><Trans t="collection.share-modal.title"/></Modal.Title>
                <button type="button"
                        className="close"
                        data-dismiss="modal"
                        aria-label={closeText}
                        onClick={() => handleClose()}>
                    <span aria-hidden="true"><CloseIcon width="22" height="22"/></span>
                </button>
            </Modal.Header>
            <Modal.Body className="bg-light">
                {isLoading ? (
                    <LoadingIndicator className="my-4"/>
                ) : (
                    <Fragment>
                        <div className="row">
                            <div className="col-6"/>
                            {permissionTypes.map(({id, translationKey}) => (
                                <div key={id} className="col-2">
                                    <div className="font-weight-bold"><Trans t={translationKey}/></div>
                                </div>
                            ))}
                        </div>

                        <hr className="my-2"/>

                        {permissions && permissions.length ? (
                            <ul className="list-unstyled">
                                {permissions.map((permissionEntry) => (
                                    <li key={permissionEntry.id}>
                                        <div className="row align-items-center">
                                            <div className="col-6">
                                                {permissionEntry.name}
                                            </div>
                                            {permissionTypes.map(({id, translationKey}) => (
                                                <div key={id} className="col-2">
                                                    <Radio checked={id === permissionEntry.permission}
                                                           onChange={() => handleRadioChange(permissionEntry.id, id)}>
                                                        <div className="sr-only">
                                                            {permissionEntry.name} {translationKey}
                                                        </div>
                                                    </Radio>
                                                </div>
                                            ))}
                                            <div className="col text-right">
                                                <button type="button"
                                                        className="btn btn-dark icon-btn"
                                                        onClick={() => removeItem(permissionEntry)}>
                                                    <TrashIcon className="icon-btn__icon"/>
                                                </button>
                                            </div>
                                        </div>
                                        <hr className="my-2"/>
                                    </li>
                                ))}
                            </ul>
                        ) : null}

                        <div className="row align-items-center mt-3">
                            <div className="col-sm-6 col-12">
                                <AsyncSelect className={`react-select`}
                                             classNamePrefix={`react-select`}
                                             value={null}
                                             cacheOptions
                                             filterOption={isOptionAlreadyUsed}
                                             onChange={addPermissionRowHandler}
                                             loadOptions={getAsyncOptions}
                                             placeholder={<Trans t="collection.share-modal.input.placeholder"/>}
                                             loadingMessage={() =>
                                                 <Trans t="collection.share-modal.input.loading"/>}
                                             noOptionsMessage={({inputValue}) =>
                                                 (!inputValue || inputValue === '')
                                                     ? null
                                                     : <Trans t="collection.share-modal.input.no-match"/>}/>
                            </div>
                        </div>
                    </Fragment>
                )}
            </Modal.Body>
            <Modal.Footer className="justify-content-center">
                <ButtonWithIcon
                    type="button"
                    variant="primary"
                    disabled={isLoading}
                    Icon={<ShareIcon width="16" height="16"/>}
                    onClick={() => {
                        onSubmit(permissions);
                        handleClose();
                    }}>
                    <Trans t="collection.share-modal.cta"/>
                </ButtonWithIcon>
            </Modal.Footer>
        </Modal>
    )
}

export function mapStateToProps(state, {collectionId}) {
    let shareListFetchingState = getShareListRequestStateByCollectionId(state, collectionId);

    return {
        shareListFetchingState,
        shareList: getShareListByCollectionId(state, collectionId),
        isLoading: shareListFetchingState === FETCHING || shareListFetchingState === NOT_ASKED
    };
}

export const mapDispatchToProps = (dispatch, {collectionId}) => {
    return {
        onRequestData: () => dispatch(requestCollectionShareList({collectionId})),
        onSubmit: (permissions) => dispatch(updateCollectionShareList({collectionId, permissions}))
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(ShareModal);


const debouncedSmartSuggest = debounce(300, (callback, params) => {
    getShareSmartSuggest(params).response.then(callback);
});
