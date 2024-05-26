/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


export function bottom(event) {
    return {
        bottom: 0,
        left: "50%",
        transform: "translate(-50%, 0)"
    }
}

export function top(event) {
    return {
        top: 0,
        left: "50%",
        transform: "translate(-50%, -100%)"
    }
}

export function left(event) {
    return {};
}

export function right(event) {
    return {};
}