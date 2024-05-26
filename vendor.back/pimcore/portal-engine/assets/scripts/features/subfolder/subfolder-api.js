/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {fetchJson} from "~portal-engine/scripts/utils/fetch";
import {getConfig} from "~portal-engine/scripts/utils/general";

export function createSubfolder(elementType, currentFolder, name) {
    return subfolderApi("/_portal-engine/api/folder/create", elementType, currentFolder, name);
}

export function renameSubfolder(elementType, currentFolder, name) {
    return subfolderApi("/_portal-engine/api/folder/rename", elementType, currentFolder, name);
}

export function deleteSubfolder(elementType, currentFolder) {
    return subfolderApi("/_portal-engine/api/folder/delete", elementType, currentFolder);
}

function subfolderApi(baseUrl, elementType, currentFolder, name) {
    currentFolder = encodeURI(currentFolder);
    name = encodeURI(name);

    return fetchJson(`${baseUrl}?dataPoolId=${getConfig("currentDataPool.id")}&elementType=${elementType}&currentFolder=${currentFolder}&name=${name}`, {
        method: "POST"
    });
}