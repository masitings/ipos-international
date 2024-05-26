/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from "react";
import {
    getModalCollectionId,
    getModalDataPools, getModalItemIds, getModalShareUrl, getModalSubmitError, getModalSubmitState, isModalOpen,
} from "~portal-engine/scripts/features/public-share/public-share-selectors";
import {connect} from "react-redux";
import PublicShareModal from "~portal-engine/scripts/features/public-share/components/PublicShareModal";
import {closedPublicShareModal, publicShare} from "~portal-engine/scripts/features/public-share/public-share-actions";
import {FETCHING} from "~portal-engine/scripts/consts/fetchingStates";

export function mapStateToProps(state, {id, ...props}) {
    let submitState = getModalSubmitState(state);
    return {
        itemIds: getModalItemIds(state),
        collectionId: getModalCollectionId(state),
        dataPools: getModalDataPools(state),
        isOpen: isModalOpen(state),
        isLoading: submitState === FETCHING,
        submitState: submitState,
        shareUrl: getModalShareUrl(state) || '',
        error: getModalSubmitError(state),
        ...props
    };
}

export const mapDispatchToProps = (dispatch) => {
    return {
        onSubmit: (payload) => {
            dispatch(publicShare(payload));
        },
        onClose: () => {
            dispatch(closedPublicShareModal())
        }
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(PublicShareModal);