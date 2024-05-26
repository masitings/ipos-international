/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


export function filterRemoved(metadata) {
    return ([key, data]) => {
        return !isRemoved(metadata, key);
    }
}

export function isRemoved(metadata, prefix) {
    if (!Array.isArray(metadata.removed)) {
        return false;
    }

    return metadata.removed.includes(prefix);
}