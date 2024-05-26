/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


// export const getTasks = state => state.tasks.byId;
export const getAllTasks = state => state.tasks.byId;
export const getFetchingState = state => state.tasks.fetchingState;
export const getTaskById = (state, id) => state.tasks.byId[id];