/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from "react";
import {connect} from "react-redux";
import {getSelectedFolderPath,} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import ClearFilterButton from "~portal-engine/scripts/components/filter/clear/ClearFilterButton";
import {getConfig} from "~portal-engine/scripts/utils/general";
import {selectFolder} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";

export const mapStateToProps = state => ({
    folderPath: getSelectedFolderPath(state)
});

const rootPath = getConfig('list.folders.root.path') || '/';

export function DataPoolClearFolderFilterButtons({
    folderPath = rootPath,
    dispatch
}) {

    if (!folderPath || folderPath === rootPath) {
        return null;
    }

    return (
        <Fragment>
            <ClearFilterButton key={`folder`}
                               onClick={() => dispatch(selectFolder({path: rootPath}))}
                               className="vertical-gutter__item mr-2">
                {folderPath.slice(folderPath.lastIndexOf('/') + 1)}
            </ClearFilterButton>
        </Fragment>

    )
}

export default connect(mapStateToProps)(DataPoolClearFolderFilterButtons)