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
import ListNavigation from "~portal-engine/scripts/components/ListNavigation";
import {
    getNavigationType,
    getSelectedFolderPath,
    getSelectedTagIds
} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import {navigationTypeChanged} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import DataPoolFolderTree from "~portal-engine/scripts/features/data-pool-list/components/DataPoolFolderTree";
import DataPoolTagTree from "~portal-engine/scripts/features/data-pool-list/components/DataPoolTagTree";

export const mapStateToProps = state => ({
    navigationType: getNavigationType(state),
    selectedFolderPath: getSelectedFolderPath(state),
    selectedTagIds: getSelectedTagIds(state),
    FolderTreeComponent: DataPoolFolderTree,
    TagTreeComponent: DataPoolTagTree,
});

export const mapDispatchToProps = {
    onSelect: navigationTypeChanged
};

export default connect(mapStateToProps, mapDispatchToProps)(ListNavigation);