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
import {removeFromCollection} from "~portal-engine/scripts/features/collections/collections-actions";
import DataPoolListTableRow, {mapStateToProps} from "~portal-engine/scripts/features/data-pool-list/components/DataPoolListTableRow";

export const mapDispatchToProps = (dispatch) => {
    return {
        actionHandler: {
            onRemoveFromCollection: (id) => dispatch(removeFromCollection({
                ids: [id],
                dataPoolId: getConfig("currentDataPool.id"),
                collectionId: getConfig("collection.id"),
            }))
        }
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(DataPoolListTableRow);