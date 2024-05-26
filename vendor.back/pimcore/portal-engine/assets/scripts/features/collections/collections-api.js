/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {abortAbleFetch, fetchJson, prepareFetchPromise} from "~portal-engine/scripts/utils/fetch";
import {addParamsObjectToURL, addParamTupleArrayToURL} from "~portal-engine/scripts/utils/utils";
import {getConfig} from "~portal-engine/scripts/utils/general";

export const getCollectionList = (params) => {
    let {abort, response} = abortAbleFetch(
        addParamTupleArrayToURL(`/_portal-engine/api/collection/overview?documentId=${getConfig('documentId')}`, [['_', new Date().getTime()], ...params])
    );

    return {
        abort,
        response: prepareFetchPromise(response)
    }
};

export const getAllEditableCollections = () => {
    let {abort, response} = abortAbleFetch(`/_portal-engine/api/collection/list`);

    return {
        abort,
        response: prepareFetchPromise(response)
    }
};

export const addToCollection = ({dataPoolId, ids, collectionId}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/collection/add-to-collection/${collectionId}?documentId=${getConfig('documentId')}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                dataPoolId,
                'selectedIds': ids
            })
        })
    );

export const removeFromCollection = ({dataPoolId, ids, collectionId}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/collection/remove-from-collection/${collectionId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                dataPoolId,
                selectedIds: ids
            })
        })
    );

export const createCollection = ({name}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/collection/create`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name
                })
            }
        )
    );

export const deleteCollection = ({id}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/collection/delete/${id}`, {
                method: 'DELETE',
            }
        )
    );

export const renameCollection = ({id, name}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/collection/rename/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name
                })
            }
        )
    );


// Sharing
const normalizeShareResponse = ({data, ...rest}) => ({
    ...rest,
    data: data.map(({permission, userOrGroup: {name, id, type}}) => ({permission, name, id, type}))
});


export const getShareList = ({collectionId}) => {
    let {abort, response} = abortAbleFetch(`/_portal-engine/api/collection/share-list/${collectionId}`);

    return {
        abort,
        response: prepareFetchPromise(response).then(normalizeShareResponse)
    }
};

export const getShareSmartSuggest = ({text}) => {
    let {abort, response} = abortAbleFetch(addParamsObjectToURL(
        `/_portal-engine/api/collection/share-list/users-and-groups`,
        {q: text}));

    return {
        abort,
        response: prepareFetchPromise(response)
    }
};

export const updateCollectionShareList = ({collectionId, permissions = []}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/collection/update-sharing/${collectionId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                data: permissions.map(({id, type, permission}) => ({
                    permission,
                    userOrGroup: {
                        id,
                        type,
                    }
                }))
            })
        })
    ).then(normalizeShareResponse);

// Detail Actions
export const fetchDetailActions = ({collectionId}) => {
    return fetchJson(`/_portal-engine/api/collection/detail-actions/${collectionId}`);
};