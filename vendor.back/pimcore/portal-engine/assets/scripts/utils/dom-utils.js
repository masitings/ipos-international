/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {curry} from "~portal-engine/scripts/utils/utils";

// Dom traversing
export const find = selector => findIn(selector, document);
export const findAll = selector => findAllIn(selector, document);
export const findIn = curry((selector, element) => element.querySelector(selector));
export const findAllIn = curry((selector, element) => toArray(element.querySelectorAll(selector)));

export const closest = curry((selector, element) => element.closest(selector));

// Class manipulation
export const addClass = curry((className, element) => {
    element.classList.add(className);
    return element;
});
export const removeClass = curry((className, element) => {
    element.classList.remove(className);
    return element;
});
export const toggleClass = curry((className, element) => {
    element.classList.toggle(className);
    return element;
});
export const hasClass = curry((className, element) => element.classList.contains(className));

const toArray = iterable => Array.from(iterable);