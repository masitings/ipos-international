/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {
    getItemById,
} from "~portal-engine/scripts/features/public-share/public-share-selectors";
import {connect} from "react-redux";
import PublicShareModal from "~portal-engine/scripts/features/public-share/components/PublicShareModal";
import React from "react";
import {mapObject, noop} from "~portal-engine/scripts/utils/utils";
import {downloadConfigToSelectionState} from "~portal-engine/scripts/features/download/download-utils";
import {publicShareEdit} from "~portal-engine/scripts/features/public-share/public-share-actions";

export function mapStateToProps(state, {id, ...props}) {
    let publicShare = getItemById(state, id);
    let dataPools = publicShare.dataPools;

    let downloadConfigByDataPoolId = Array.isArray(publicShare.configs)
        ? {[dataPools[0].id]: publicShare.configs}
        : publicShare.configs;
    
    let downloadSelectionStateByDataPoolId = mapObject(
        downloadConfigByDataPoolId,
        (dataPoolId, config) => downloadConfigToSelectionState(config)
    );

    return {
        name: publicShare.name,
        expiryDate: new Date(publicShare.expiryDate * 1000).toISOString().split('T')[0],
        showTermsText: publicShare.showTermsText,
        termsText: publicShare.termsText,
        dataPools: publicShare.dataPools,
        downloadSelectionStateByDataPoolId,
        ...props
    };
}

export const mapDispatchToProps = (dispatch, {id, onClose = noop}) => {
    return {
        onSubmit: (payload) => {
            dispatch(publicShareEdit({
                id,
                ...payload
            }));
            onClose();
        }
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(PublicShareModal);