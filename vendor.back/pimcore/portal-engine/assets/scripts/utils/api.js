/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


let endPoints = {};

export function setAPIEndPoint(key, url) {
    endPoints[key] = url;
}

export function getAPIEndPoint(key) {
    if (!endPoints[key]) {
        console.error(`Missing api endpoint "${key}"`);
        return ''
    }

    return endPoints[key];
}