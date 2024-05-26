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
import {toggleTagSelection} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {getSelectedTagIds} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import TagTree from "~portal-engine/scripts/features/tags/coponents/TagTree";

export const mapStateToProps = state => ({
    selectedIds: getSelectedTagIds(state)
});

export const mapDispatchToProps = {
    onSelectionChange: toggleTagSelection
};

export default connect(mapStateToProps, mapDispatchToProps)(TagTree)