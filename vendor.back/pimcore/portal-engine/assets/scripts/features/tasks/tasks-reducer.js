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
import {fetchingStateReducer} from "~portal-engine/scripts/utils/fetch";
import {NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";

import {
    TASKS_REQUESTED,
    TASKS_FETCHED,
    TASKS_FAILED,
    TASK_DELETED,
    interactedTask,
} from "~portal-engine/scripts/features/tasks/tasks-actions";

const initialState = {
    fetchingState: NOT_ASKED,
    fetchingError: null,
    byId: {}
};

export default createReducer(initialState, {
    ...fetchingStateReducer(TASKS_REQUESTED, TASKS_FETCHED, TASKS_FAILED, "fetchingState", "fetchingError", (state, {hasQueuedTasks, tasks, type}) => {
        state.allIds = [];
        tasks.map((task) => {
            state.byId[task.id] = {...task, timestamp: task.createdAt};
        });
    }),
    [TASK_DELETED.SUCCEEDED]: (state, {payload: {data, id}}) => {
        delete state.byId[id];
    },
    [interactedTask]: (state, {payload: {id}}) => {
        delete state.byId[id];
    },
});