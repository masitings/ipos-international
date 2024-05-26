/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {abortAbleFetch, prepareFetchPromise} from "~portal-engine/scripts/utils/fetch";
import {addParamTupleArrayToURL} from "~portal-engine/scripts/utils/utils";
import {getLanguage} from "~portal-engine/scripts/utils/intl";

export const createPublicShare = ({name, downloadConfigs, expiryDate, showTermsText, termsText, dataPoolConfigId, itemIds, collectionId}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/public-share/create`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    _locale: getLanguage(),
                    name,
                    downloadConfigs,
                    expiryDate,
                    showTermsText,
                    termsText,
                    dataPoolConfigId,
                    elementIds: itemIds,
                    collectionId
                })
            }
        )
    );

export const updatePublicShare = ({id, name, downloadConfigs, expiryDate, showTermsText, termsText}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/public-share/update/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    _locale: getLanguage(),
                    name,
                    downloadConfigs,
                    expiryDate,
                    showTermsText,
                    termsText,
                })
            }
        )
    );

export const getPublicShareList = (params) => {
    let {abort, response} = abortAbleFetch(
        addParamTupleArrayToURL(`/_portal-engine/api/public-share/list`, [['_locale', getLanguage(), '_', new Date().getTime()], ...params])
    );

    return {
        abort,
        response: prepareFetchPromise(response)
    }
};

export const deletePublicShare = ({id}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/public-share/delete/${id}`, {
                method: 'DELETE',
            }
        )
    );