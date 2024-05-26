/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


export function getSelectionKey({dataPoolId, collectionId, publicShareHash}) {
    let keyParts = [];
    if (publicShareHash) {
        keyParts.push(`publicShare-${publicShareHash}`)
    }

    if (collectionId) {
        keyParts.push(`collection-${collectionId}`)
    }

    keyParts.push(dataPoolId);

    return keyParts.join('-');
}