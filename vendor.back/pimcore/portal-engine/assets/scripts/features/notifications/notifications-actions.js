/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {createAction} from "@reduxjs/toolkit";
import { v4 as uuid } from 'uuid';
import {dispatch} from '~portal-engine/scripts/store'

export const addNotification = createAction('notification/added', ({...params}) => ({
    payload: {
        ...params,
        id: uuid(),
        timestamp: Math.round(new Date().getTime() / 1000),
    }
}));

export const deleteNotification = createAction('notification/deleted', ({id}) => ({
    payload: {id}
}));

export const clearAllNotifications = createAction('notification/clear-all');

// no action creator - dispatches the function immediately
export const showNotification = (...params) => dispatch(
    addNotification(...params)
);