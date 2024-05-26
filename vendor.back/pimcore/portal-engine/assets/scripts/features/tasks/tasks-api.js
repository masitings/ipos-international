/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {prepareFetchPromise} from "~portal-engine/scripts/utils/fetch";
import {addParamsObjectToURL} from "~portal-engine/scripts/utils/utils";
import {getConfig} from "~portal-engine/scripts/utils/general";

const publicShareHash = getConfig('publicShare.hash');
const additionalParams = {
    ...(publicShareHash ? {publicShareHash} : null)
};

// todo add public share hash
export const fetchTaskList = () =>
    prepareFetchPromise(
        fetch(
            addParamsObjectToURL(`/_portal-engine/api/batch-task/list`, additionalParams)
        )
    );

export const deleteTask = ({id}) =>
    prepareFetchPromise(
        fetch(addParamsObjectToURL(`/_portal-engine/api/batch-task/delete/${id}`, additionalParams), {
                method: 'DELETE',
            }
        )
    );