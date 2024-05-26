/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


export const getAllNotificationIds = state => state.notifications.allIds;
export const getAllNotifications = state => state.notifications.byId;
export const getNotificationById = (state, id) => state.notifications.byId[id];