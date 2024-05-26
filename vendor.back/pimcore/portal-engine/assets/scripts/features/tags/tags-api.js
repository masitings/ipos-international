/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {abortAbleFetch} from "~portal-engine/scripts/utils/fetch";
import {addParamsObjectToURL, addParamTupleArrayToURL} from "~portal-engine/scripts/utils/utils";
import {getAPIEndPoint} from "~portal-engine/scripts/utils/api";

export function fetchUsedTags(ids = []) {
    let url = getAPIEndPoint('tags');
    url = addParamTupleArrayToURL(url, ids.map(id => (['checkedIds[]', id])));
    return fetchTags(url);
}

export function fetchUnfilteredTags() {
    let url = addParamsObjectToURL(getAPIEndPoint('tags'), {includeAll: true});
    return fetchTags(url);
}

function fetchTags(url) {
    let {abort, response} = abortAbleFetch(url);

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
