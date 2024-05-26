/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {addParamsObjectToURL, addParamsArrayToURL, addParamTupleArrayToURL} from "~portal-engine/scripts/utils/utils";
import {abortAbleFetch, prepareFetchPromise} from "~portal-engine/scripts/utils/fetch";
import {getConfig} from "~portal-engine/scripts/utils/general";

export function fetchSearchResult(params = []) {
    let {abort, response} = abortAbleFetch(addParamsArrayToURL(`/_portal-engine/api/search/smart-suggest?documentId=${getConfig("documentId")}`, params));

    return {
        abort,
        response: response.then(response => response.json())
            .catch(payload => {
                return payload.error
                    ? Promise.reject(payload.error)
                    : Promise.reject(payload)
            })

    }
}

export function gotoSearchResult(keywords) {
    window.location.href = getConfig('searchUrl') + '?' + keywords.map((item) => 'q[]=' + item).join('&');
}

export const getSearchList = (params) => {
    let {abort, response} = abortAbleFetch(addParamTupleArrayToURL(`/_portal-engine/api/saved-search/overview?documentId=${getConfig('documentId')}`, params));

    return {
        abort,
        response: prepareFetchPromise(response)
    }
};

export const saveSearch = ({urlQuery , name}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/saved-search/create`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    urlQuery, name
                })
            }
        )
    );


export const deleteSearch = ({id}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/saved-search/delete/${id}`, {
                method: 'DELETE',
            }
        )
    );

export const deleteSharedSearch = ({id}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/saved-search/remove-user-sharing/${id}`, {
                method: 'DELETE',
            }
        )
    );

export const renameSearch = ({id, name}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/saved-search/rename/${id}`, {
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


export const getSearchShareList = ({searchId}) => {
    let {abort, response} = abortAbleFetch(`/_portal-engine/api/saved-search/share-list/${searchId}`);

    return {
        abort,
        response: prepareFetchPromise(response).then(normalizeShareResponse)
    }
};

export const getSearchShareSmartSuggest = ({text}) => {
    let {abort, response} = abortAbleFetch(addParamsObjectToURL(
        `/_portal-engine/api/saved-search/share-list/users-and-groups`,
        {q: text}));

    return {
        abort,
        response: prepareFetchPromise(response)
    }
};

export const updateSearchShareList = ({searchId, shares = []}) =>
    prepareFetchPromise(
        fetch(`/_portal-engine/api/saved-search/update-sharing/${searchId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                data: shares.map(({id, type, share}) => ({
                    share,
                    userOrGroup: {
                        id,
                        type,
                    }
                }))
            })
        })
    ).then(normalizeShareResponse);