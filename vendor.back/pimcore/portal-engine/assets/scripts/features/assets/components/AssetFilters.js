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
import DataPoolFilter from "~portal-engine/scripts/features/data-pool-list/components/DataPoolFilter";
import {connect} from "react-redux";
import {
    getCurrentPageNumber,
    getFetchingStateByPage,
    getPermissions,
    getUploadFolder
} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import {MD_UP} from "~portal-engine/scripts/consts/mediaQueries";
import Media from "react-media";
import {FETCHING, NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import {getFilterBarButtons} from "~portal-engine/scripts/features/assets/assets-utils";

export function Filters({permissions = {}, uploadFolder = null, isFetching = true, ...props}) {
    const additionalEntries = getFilterBarButtons({
        uploadFolder,
        isFetching,
        permissions,
        btnProps: {
            block: false
        }
    });

    return (
        <Media query={MD_UP}>
            {matches => matches ? (
                <DataPoolFilter {...props} className="full-height-layout__fit" additionalEntries={additionalEntries}/>
            ) : (
                <DataPoolFilter {...props} className="btn-row__btn"/>
            )}
        </Media>
    )
}

export function mapStateToProps(state) {
    const currentPageNumber = getCurrentPageNumber(state);
    const currentPageFetchingState = getFetchingStateByPage(state, currentPageNumber);

    return {
        isFetching: currentPageFetchingState === FETCHING || currentPageFetchingState === NOT_ASKED,
        permissions: getPermissions(state),
        uploadFolder: getUploadFolder(state)
    }
}

export default connect(mapStateToProps)(Filters);