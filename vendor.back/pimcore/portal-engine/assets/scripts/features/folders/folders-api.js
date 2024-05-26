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
import {addParamTupleArrayToURL} from "~portal-engine/scripts/utils/utils";
import {getAPIEndPoint} from "~portal-engine/scripts/utils/api";

export function fetchFolders(path, page = 1) {
    let {abort, response} = abortAbleFetch(addParamTupleArrayToURL(getAPIEndPoint('folders'), [['folder', path], ['page', page]]));

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