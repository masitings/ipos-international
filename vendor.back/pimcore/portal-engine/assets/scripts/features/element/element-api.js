/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {getConfig} from "~portal-engine/scripts/utils/general";
import {fetchJson} from "~portal-engine/scripts/utils/fetch";
import {addParamsObjectToURL} from "~portal-engine/scripts/utils/utils";

const publicShareHash = getConfig('publicShare.hash');
const additionalParams = {
    ...(publicShareHash ? {publicShareHash} : null)
};

export function fetchValidLanguages() {
    return fetchJson(addParamsObjectToURL(`/_portal-engine/api/translation/valid-languages`, {
        dataPoolId: getConfig("currentDataPool.id"),
        ...additionalParams
    }));
}