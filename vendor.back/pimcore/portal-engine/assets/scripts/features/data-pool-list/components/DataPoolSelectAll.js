/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {connect} from "react-redux";
import React from "react";
import {selectAll} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {getConfig} from "~portal-engine/scripts/utils/general";
import {noop} from "~portal-engine/scripts/utils/utils";

export function DataPoolSelectAll({
    children,
    onClick = noop
}) {
    return (
        <button type="button"
                className="btn btn-link font-weight-bold btn-sm"
                onClick={() => onClick()}>
            <span className="selection-indicator selection-indicator--in-text is-selected mr-2"/>
            {children}
        </button>
    );
}

// export const mapStateToProps = () => {};
export const mapDispatchToProps = {
    onClick: () => selectAll({
        dataPoolId: getConfig("currentDataPool.id"),
        collectionId: getConfig("collection.id"),
        publicShareHash: getConfig('publicShare.hash')
    })
};

export default connect(null, mapDispatchToProps)(DataPoolSelectAll);