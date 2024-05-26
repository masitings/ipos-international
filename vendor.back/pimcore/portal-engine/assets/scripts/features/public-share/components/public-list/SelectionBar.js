/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from 'react';
import {connect} from "react-redux";
import {getConfig} from "~portal-engine/scripts/utils/general";
import {mapStateToProps} from "~portal-engine/scripts/features/data-pool-list/components/DataPoolSelectionBar";
import SelectionBar from "~portal-engine/scripts/components/SelectionBar";
import {
    requestSelectedItems,
    toggleSelectionAll
} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {directDownloadClicked} from "~portal-engine/scripts/features/download/download-actions";
import {filterActionHandlerByPermissions} from "~portal-engine/scripts/components/actions";

export const mapDispatchToProps = (dispatch) => {
    return {
        onShowSelected: (selectedIds) => dispatch(requestSelectedItems(selectedIds)),
        onDeSelectAll: (selectedIds) =>
            dispatch(toggleSelectionAll({
                ids: selectedIds,
                isSelected: false,
                dataPoolId: getConfig("currentDataPool.id"),
                publicShareHash: getConfig('publicShare.hash'),
            })),
        actionHandler: {
            onDownload: (ids) => dispatch(directDownloadClicked({
                ids: ids,
                dataPoolId: getConfig("currentDataPool.id")
            }))
        }
    }
};

export default connect(mapStateToProps, mapDispatchToProps)(props => {
    let {permissions, actionHandler} = props;

    actionHandler = filterActionHandlerByPermissions({
        permissions,
        actionHandler: {
            ...actionHandler,
            onDelete: confirm
        }
    });

    return <SelectionBar {...props} actionHandler={actionHandler}/>
});