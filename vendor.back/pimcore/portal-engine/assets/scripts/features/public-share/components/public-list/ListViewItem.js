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
import {mapStateToProps} from "~portal-engine/scripts/features/data-pool-list/components/DataPoolListTableRow";
import DataTableRow from "~portal-engine/scripts/components/DataTableRow";
import {toggleSelection} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {directDownloadClicked} from "~portal-engine/scripts/features/download/download-actions";
import {filterActionHandlerByPermissions} from "~portal-engine/scripts/components/actions";

export const mapDispatchToProps = (dispatch, {id, actionHandler}) => {
    return {
        onSelectedToggle: (isSelected) => dispatch(toggleSelection({
            id,
            isSelected,
            dataPoolId: getConfig("currentDataPool.id"),
            collectionId: getConfig("collection.id")
        })),
        actionHandler: {
            onDownload: (id) => dispatch(directDownloadClicked({
                ids: [id],
                dataPoolId: getConfig("currentDataPool.id")
            }))
        }
    }
};

export function ListViewItem(props) {
    let {permissions, actionHandler} = props;

    actionHandler = filterActionHandlerByPermissions({
        permissions,
        actionHandler: {
            ...actionHandler,
            onDelete: confirm
        }
    });

    return <DataTableRow {...props} actionHandler={actionHandler}/>
}

export default connect(mapStateToProps, mapDispatchToProps)(ListViewItem);