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
import {selectors} from "~portal-engine/scripts/sliceHelper/list/list-selectos";

export const isPublicShare = state => state.publicShare.isPublicShare;
export const getShowTermsText = state => state.publicShare.showTermsText;
export const getTermsText = state => state.publicShare.termsText;

export const isModalOpen = state => state.publicShare.isModalOpen;
export const getModalDataPools = state => state.publicShare.modalDataPools;
export const getModalItemIds = state => state.publicShare.modalItemIds;
export const getModalCollectionId = state => state.publicShare.modalCollectionId;
export const getModalSubmitState = state => state.publicShare.submitState;
export const getModalShareUrl = state => state.publicShare.shareUrl;
export const getModalSubmitError = state => state.publicShare.submitError;

const listSelectors =
    mapObject(selectors, (_, selector) =>
        (state, ...params) =>
            selector(state.publicShare, ...params)
    );

export const {
    getCurrentPageNumber,
    getPageCount,
    getResultCount,
    getPageSize,
    getIdsByPageNumber,
    getItemById,
    getFetchingStateByPage,
    getFetchingMessageByPage,
    getListParams,
    getListParamNames,
} = listSelectors;