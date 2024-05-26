/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from "react";
import {createFetchActions} from "~portal-engine/scripts/utils/fetch";
import {createAction} from "@reduxjs/toolkit";
import * as api from "~portal-engine/scripts/features/tasks/tasks-api";
import {errorToPayload} from "~portal-engine/scripts/utils/fetch";
import {showNotification, deleteNotification} from "~portal-engine/scripts/features/notifications/notifications-actions";
import {SUCCESS, TASK} from "~portal-engine/scripts/consts/notification-types";

export const TASKS_REQUESTED = "tasks/requested";
export const TASKS_FAILED = "tasks/failed";

export function fetchTasks() {
    return function (dispatch) {
        dispatch({type: TASKS_REQUESTED});

        let request = api.fetchTaskList();
        request.then((response) => {
                dispatch(tasksFetched(response.data));

                // still has queued tasks --> poll
                if (response.data.hasQueuedTasks) {
                    setTimeout(() => {
                        dispatch(fetchTasks());
                    }, 5000);
                }
            })
            .catch((error) => {
                dispatch({type: TASKS_FAILED, ...errorToPayload(error)})
            });
    }
}

export const TASKS_FETCHED = "tasks/fetched";
export function tasksFetched(response) {
    return {
        type: TASKS_FETCHED,
        payload: {...response, type: TASK}
    }
}

export const interactedTask = createAction('task/interacted', ({id}) => ({
    payload: {
        id
    }
}));

export const {
    actionTypes: TASK_DELETED,
    actionCreator: deleteTask
} = createFetchActions(
    'task/delete',
    (state, {id}) => {
        let request = api.deleteTask({id});

        request.then(({success}) => {
            if (success) {
                fetchTasks()
            }
        });

        return request;
    }
);