/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {createReducer} from "@reduxjs/toolkit";
import * as filterListSlice from "~portal-engine/scripts/sliceHelper/filter-list/filter-list-reducer";
import {DATA_POOL_LIST_ACTION_TYPES} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions"
import {getConfig} from "~portal-engine/scripts/utils/general";

const initialState = {
    id: getConfig('currentDataPool.id'),
    collectionId: getConfig('collection.id'),
    ...filterListSlice.initialState,
};

export default createReducer(initialState, {
    ...filterListSlice.createReducer({
        ACTION_TYPES: DATA_POOL_LIST_ACTION_TYPES,
        payloadMapper: ({data}) => {
            return ({
                data: {
                    ...data,
                    entries: (data.items || [])
                }
            });
        }
    }),
});
