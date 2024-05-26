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
import {
    addNotification,
    clearAllNotifications,
    deleteNotification
} from "~portal-engine/scripts/features/notifications/notifications-actions";

const initialState = {
    byId: {},
    allIds: [
    ]
};

export default createReducer(initialState, {
    [addNotification]: (state, {payload: {id, ...params}}) => {
        state.allIds.push(id);
        state.byId[id] = {...params, id};
    },
    [deleteNotification]: (state, {payload: {id}}) => {
        state.allIds = state.allIds.filter(currentId => {
            return currentId !== id;
        });
        delete state.byId[id];
    },
    [clearAllNotifications]: (state) => {
        state.allIds = [];
        state.byId = {};
    }
})
