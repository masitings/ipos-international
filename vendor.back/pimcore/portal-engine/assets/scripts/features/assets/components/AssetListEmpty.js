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
import {getDropZoneLabel} from "~portal-engine/scripts/features/assets/assets-utils";
import {noop} from "~portal-engine/scripts/utils/utils";
import {fileDropped} from "~portal-engine/scripts/features/upload/upload-actions";
import Trans from "~portal-engine/scripts/components/Trans";

export function EmptyAssetView({uploadPermission = false, onDrop = noop}) {
    return (
        <InvisibleDropZone disabled={!uploadPermission}
                           label={getDropZoneLabel(uploadPermission)}
                           onFilesDropped={onDrop}>

            <div className="row justify-content-center my-4 my-lg-5">
                <div className="col-6">
                    <div className="h3 text-center"><Trans t="listing.no-results"/></div>
                </div>
            </div>
        </InvisibleDropZone>
    )
}

export const mapStateToProps = state => ({
    uploadPermission: getPermissions(state).create !== false
});

export const mapPropsToDispatch = {
    onDrop: fileDropped
};

export default connect(mapStateToProps, mapPropsToDispatch)(EmptyAssetView);