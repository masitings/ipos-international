/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from "react";
import {addAdapters} from "~portal-engine/scripts/features/element/element-layout";
import * as adaptersByName from "~portal-engine/scripts/features/data-objects/render-adapters";

export function setupDataObjectLayout() {
    addAdapters(adaptersByName);
}

let fieldcollectionLayoutDefinitions = {};
let objectbrickLayoutDefinitions = {};
let classificationStoreLayoutDefinitions = {};
let currentLayout = {};

export function getCurrentLayout() {
    return currentLayout;
}

export function setCurrentLayout(layout) {
    currentLayout = layout;
}

/**
 * @param definitions
 */
export function addFieldcollectionLayoutDefinitions(definitions) {
    if (!definitions) {
        return;
    }

    fieldcollectionLayoutDefinitions = {...fieldcollectionLayoutDefinitions, ...definitions};
}

/**
 * @param type
 * @returns {{}|null}
 */
export function getFieldcollectionLayoutDefinition(type) {
    return fieldcollectionLayoutDefinitions[type];
}

/**
 * @param definitions
 */
export function addObjectbrickLayoutDefinitions(definitions) {
    if (!definitions) {
        return;
    }

    objectbrickLayoutDefinitions = {...objectbrickLayoutDefinitions, ...definitions};
}

/**
 * @param type
 * @returns {{}|null}
 */
export function getObjectbrickLayoutDefinition(type) {
    return objectbrickLayoutDefinitions[type];
}

/**
 * @param definitions
 */
export function addClassificationStoreLayoutDefinitions(definitions) {
    if (!definitions) {
        return;
    }

    classificationStoreLayoutDefinitions = {...classificationStoreLayoutDefinitions, ...definitions};
}

/**
 * @param keyId
 * @returns {{}|null}
 */
export function getClassificationStoreLayoutDefinition(keyId) {
    return classificationStoreLayoutDefinitions[keyId];
}