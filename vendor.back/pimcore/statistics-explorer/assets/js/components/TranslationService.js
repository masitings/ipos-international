/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import handleErrors from "./fetchErrorHandler";

let translationsLoaded = false;
let translationsMap = {};

fetch(statisticsExplorerConfig.translationsUrl)
    .then(handleErrors)
    .then(res => res.json())
    .then(
        (result) => {
            translationsMap = result;
            translationsLoaded = true;
        },
    )
    .catch(error => {
        console.error(error);
        toast.error('Error loading translations.', {autoClose: false});
    })
;

export default function translate(key) {

    if(!translationsLoaded) {
        return '';
    }

    const translationKey = 'statistics_container.' + key;

    return translationsMap[translationKey] ? translationsMap[translationKey] : key;
}
