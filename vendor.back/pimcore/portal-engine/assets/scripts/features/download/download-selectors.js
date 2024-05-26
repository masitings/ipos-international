/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {mapObject} from "~portal-engine/scripts/utils/utils";

export const getAttributeById = (state, {id, dataPoolId}) =>
    state.attributesByDataPool[dataPoolId]
        ? state.attributesByDataPool[dataPoolId].byId[id]
        : null;

export const getAllAttributeIdsByDataPoolId = (state, dataPoolId) =>
    state.attributesByDataPool[dataPoolId]
        ? state.attributesByDataPool[dataPoolId].allIds
        : [];

export const getSelectedAttributesById = (state, id) => {
    let item = state.itemsById[id];

    return (!item || !item.configsById)
        ? []
        : Object.keys(item.configsById);
};
export const getSelectedAttributeFormatsById = (state, id) => {
    let item = state.itemsById[id];

    return (!item || !item.configsById)
        ? {}
        : mapObject(item.configsById, (_, {format}) => format);
};
export const getSelectedAttributeSetupsById = (state, id) => {
    let item = state.itemsById[id];

    return (!item || !item.configsById)
        ? {}
        : mapObject(item.configsById, (_, {setup}) => setup);
};

export const getModalState = state => (state.configModalOpen);
export const getConfigModalDataPoolId = state => state.configModalDataPoolId;
export const getConfigModalIds = state => state.configModalIds;
export const getConfigModalMode = state => state.configModalMode;
export const getConfigModalAttributes = state => state.configModalAttributes;

export const getConfigFetchErrorByDataPoolId = (state, dataPoolId) => state.configFetchErrorByDataPoolId[dataPoolId];
export const getConfigFetchStateByDataPoolId = (state, dataPoolId) => state.configFetchStateByDataPoolId[dataPoolId];

export const getCartDownloadFetchingState = state => state.cartDownloadFetchingMode;
export const getCartDownloadMessageType = state => state.cartDownloadMessageType;
export const getCartDownloadMessageText = state => state.cartDownloadMessageText;
export const getCartDownloadMessageTmpStoreKey = state => state.cartDownloadMessageTmpStoreKey;

export const getMultiDownloadFetchingState = state => state.multiDownloadFetchingState;
export const getMultiDownloadMessageType = state => state.multiDownloadMessageType;
export const getMultiDownloadMessageText = state => state.multiDownloadMessageText;
export const getMultiDownloadMessageTmpStoreKey = state => state.multiDownloadMessageTmpStoreKey;

export const getCollectionDownloadFetchingState = state => state.collectionDownloadFetchingState;
export const getCollectionDownloadMessageType = state => state.collectionDownloadMessageType;
export const getCollectionDownloadMessageText = state => state.collectionDownloadMessageText;
export const getCollectionDownloadMessageTmpStoreKey = state => state.collectionDownloadMessageTmpStoreKey;

export const getPublicShareDownloadFetchingState = state => state.publicShareDownloadFetchingState;
export const getPublicShareDownloadMessageType = state => state.publicShareDownloadMessageType;
export const getPublicShareDownloadMessageText = state => state.publicShareDownloadMessageText;
export const getPublicShareDownloadMessageTmpStoreKey = state => state.publicShareDownloadMessageTmpStoreKey;