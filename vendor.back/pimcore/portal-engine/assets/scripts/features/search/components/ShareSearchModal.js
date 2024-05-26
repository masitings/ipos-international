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
import {getSearchShareSmartSuggest} from "~portal-engine/scripts/features/search/search-api";
import AsyncSelect from "react-select/async";
import {debounce} from 'throttle-debounce';
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import {ReactComponent as ShareIcon} from "~portal-engine/icons/share-alt";
import {
    requestSearchShareList,
    updateSearchShareList
} from "~portal-engine/scripts/features/search/search-actions";
import {FETCHING, NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import {
    getShareListBySearchId,
    getShareListRequestStateBySearchId
} from "~portal-engine/scripts/features/search/search-selectors";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";


export function ShareModal({
    shareList,
    isOpen = false,
    shareListFetchingState = NOT_ASKED,
    isLoading = false,
    onRequestData = noop,
    onSubmit = noop,
    onClose = noop,
}) {
    const [shares, setShares] = useState(shareList);

    useEffect(() => {
        setShares(shareList);
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
                    );
                }
            }, {text: inputValue});
        });
    };

    const addPermissionRowHandler = ({value, label, type}) => {
        setShares([
            ...shares, {
                id: value,
                name: label,
                type
            }
        ]);
    };

    const isOptionAlreadyUsed = option => !shares.some(permissionEntry => permissionEntry.id === option.value);


    // Remove item
    const removeItem = (itemToRemove) => {
        setShares(
            shares.filter((permissionItem) => permissionItem !== itemToRemove)
        )
    };

    const closeText = useTranslation('search.share-modal.close');

    const handleClose = () => {
        onClose();
        setShares(shareList);
    };

    return (
        <Modal
            show={isOpen}
            onHide={() => handleClose()}
            centered
        >
            <Modal.Header>
                <Modal.Title><Trans t="search.share-modal.title"/></Modal.Title>
                <button type="button"
                        className="close"
                        data-dismiss="modal"
                        aria-label={closeText}
                        onClick={() => handleClose()}>
                    <span aria-hidden="true"><CloseIcon width="22" height="22"/></span>
                </button>
            </Modal.Header>
            <Modal.Body>
                {isLoading ? (
                    <LoadingIndicator className="my-4"/>
                ) : (
                    <Fragment>
                        {shares && shares.length ? (
                            <ul className="list-unstyled">
                                {shares.map((permissionEntry) => (
                                    <li key={permissionEntry.id}>
                                        <div className="row align-items-center">
                                            <div className="col-6">
                                                {permissionEntry.name}
                                            </div>
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

                        <div className="row justify-content-center mt-3">
                            <div className="col-md-8">
                                <AsyncSelect className={`react-select`}
                                             classNamePrefix={`react-select`}
                                             value={null}
                                             cacheOptions
                                             filterOption={isOptionAlreadyUsed}
                                             onChange={addPermissionRowHandler}
                                             loadOptions={getAsyncOptions}
                                             placeholder={<Trans t="search.share-modal.input.placeholder"/>}
                                             loadingMessage={() =>
                                                 <Trans t="search.share-modal.input.loading"/>}
                                             noOptionsMessage={({inputValue}) =>
                                                 (!inputValue || inputValue === '')
                                                     ? null
                                                     : <Trans t="search.share-modal.input.no-match"/>}/>
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
                        onSubmit(shares);
                        handleClose();
                    }}>
                    <Trans t="search.share-modal.cta"/>
                </ButtonWithIcon>
            </Modal.Footer>
        </Modal>
    )
}

export function mapStateToProps(state, {searchId}) {
    let shareListFetchingState = getShareListRequestStateBySearchId(state, searchId);

    return {
        shareListFetchingState,
        shareList: getShareListBySearchId(state, searchId),
        isLoading: shareListFetchingState === FETCHING || shareListFetchingState === NOT_ASKED
    };
}

export const mapDispatchToProps = (dispatch, {searchId}) => {
    return {
        onRequestData: () => dispatch(requestSearchShareList({searchId})),
        onSubmit: (shares) => dispatch(updateSearchShareList({searchId, shares}))
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(ShareModal);


const debouncedSmartSuggest = debounce(300, (callback, params) => {
    getSearchShareSmartSuggest(params).response.then(callback);
});
