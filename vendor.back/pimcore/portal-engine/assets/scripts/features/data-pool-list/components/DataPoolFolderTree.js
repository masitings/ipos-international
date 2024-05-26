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
import FolderTree from "~portal-engine/scripts/features/folders/components/FolderTree";
import {selectFolder} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {getSelectedFolderPath} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";

export const mapStateToProps = state => ({
    selectedPath: getSelectedFolderPath(state)
});

export const mapDispatchToProps = {
    onSelectionChange: selectFolder
};

export default connect(mapStateToProps, mapDispatchToProps)(FolderTree)
