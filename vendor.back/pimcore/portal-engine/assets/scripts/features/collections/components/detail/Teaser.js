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
import {toggleSelection} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {removeFromCollection} from "~portal-engine/scripts/features/collections/collections-actions";
import DataPoolTeaser from "~portal-engine/scripts/features/data-pool-list/components/DataPoolTeaser"

export const mapDispatchToProps = (dispatch) => {
    return {
        actionHandler: {
            onRemoveFromCollection: (id, isSelected = false) => {
                dispatch(removeFromCollection({
                    ids: [id],
                    dataPoolId: getConfig("currentDataPool.id"),
                    collectionId: getConfig("collection.id"),
                }));
                dispatch(toggleSelection({
                    id,
                    isSelected,
                    dataPoolId: getConfig("currentDataPool.id"),
                    collectionId: getConfig("collection.id")
                }))
            }
        },
        permissions: {
            delete: false,
            update: false
        }
    };
};

export default connect(null, mapDispatchToProps)(DataPoolTeaser);

