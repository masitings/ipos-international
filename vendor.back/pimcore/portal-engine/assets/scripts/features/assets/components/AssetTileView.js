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
import {connect} from "react-redux";
import {getPermissions} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import InvisibleDropZone from "~portal-engine/scripts/components/InvisibleDropZone";
import DataPoolTileView from "~portal-engine/scripts/features/data-pool-list/components/DataPoolTileView";
import {getDropZoneLabel} from "~portal-engine/scripts/features/assets/assets-utils";
import {noop} from "~portal-engine/scripts/utils/utils";
import {fileDropped} from "~portal-engine/scripts/features/upload/upload-actions";

export function TileView({uploadPermission = false, onDrop = noop, ...props}) {
    return (
        <InvisibleDropZone disabled={!uploadPermission}
                           label={getDropZoneLabel(uploadPermission)}
                           onFilesDropped={onDrop}>
            <DataPoolTileView {...props}/>
        </InvisibleDropZone>
    )
}

export const mapStateToProps = state => ({
    uploadPermission: getPermissions(state).create !== false
});

export const mapPropsToDispatch = {
    onDrop: fileDropped
};

export default connect(mapStateToProps, mapPropsToDispatch)(TileView);