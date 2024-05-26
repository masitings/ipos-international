/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {addParamTupleArrayToURL, addParamsObjectToURL} from "~portal-engine/scripts/utils/utils";
import {abortAbleFetch, prepareFetchPromise} from "~portal-engine/scripts/utils/fetch";
import {getAPIEndPoint} from "~portal-engine/scripts/utils/api";
import {getEndpoint} from "~portal-engine/scripts/utils/fetch";

export function fetchList(params = []) {
    let {abort, response} = abortAbleFetch(addParamTupleArrayToURL(getAPIEndPoint('list'), [
        ['_', new Date().getTime()],
        ...params
]));

    return {
        abort,
        response: prepareFetchPromise(response)
    }
}


export function fetchFilterStructure(params = []) {
    let {abort, response} = abortAbleFetch(addParamTupleArrayToURL(getAPIEndPoint('filters'), params));

    return {
        abort,
        response: prepareFetchPromise(response)
    }
}

export const fetchFilterStates = fetchFilterStructure;


export const getSelectedItems = (params = []) => {
    let {abort, response} = abortAbleFetch(getAPIEndPoint('list') + '&' + params.map((value) => `ids[]=${value}`).join('&') + '&pageSize=1000');

    return {
        abort,
        response: prepareFetchPromise(response)
    }
};

export function fetchAllSelectableIds(params = []) {
    let {abort, response} = abortAbleFetch(addParamTupleArrayToURL(getAPIEndPoint('all-selectable-ids-url'), [
        ['_', new Date().getTime()],
        ...params
    ]));

    return {
        abort,
        response: prepareFetchPromise(response)
    }
}

export const getSidebarItems = (params = []) => {
    let {abort, response} = abortAbleFetch(getEndpoint('resultList'));

    return {
        abort,
        response: prepareFetchPromise(response)
    }
};

export const relocateItem = ({ids, folder, dataPoolId, fromDetailPage}) => {
    let {abort, response} = abortAbleFetch(`/_portal-engine/api/asset/relocate/${ids}?targetFolder=${folder}&dataPoolId=${dataPoolId}&fromDetailPage=${fromDetailPage}`);

    return {
        abort,
        response: prepareFetchPromise(response)
    }
};

export function relocateMultiItems({ids, folder, dataPoolId}) {
    let data = new FormData();
    data.append('ids', ids);

    let {abort, response} = abortAbleFetch(addParamsObjectToURL('/_portal-engine/api/asset/trigger-batch-relocate', {
        dataPoolId,
        'targetPage': window.location.href,
        'targetFolder': folder
    }), {
        method: 'POST',
        body: data
    });

    return {
        abort,
        response: prepareFetchPromise(response)
    };
}

export function deleteItem({ids, dataPoolId}) {
    let {abort, response} = abortAbleFetch(`/_portal-engine/api/asset/delete/${ids}?dataPoolId=${dataPoolId}`);

    return {
        abort,
        response: prepareFetchPromise(response)
    }
}

export function deleteMultiItems({ids, dataPoolId}) {
    let data = new FormData();
    data.append('ids', ids);

    let {abort, response} = abortAbleFetch(addParamsObjectToURL('/_portal-engine/api/asset/trigger-batch-delete', {
        dataPoolId,
        'targetPage': window.location.href,
    }), {
        method: 'POST',
        body: data
    });

    return {
        abort,
        response: prepareFetchPromise(response)
    };
}