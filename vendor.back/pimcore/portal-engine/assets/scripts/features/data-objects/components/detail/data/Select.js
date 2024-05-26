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

export function extractSelectLabel(value) {
    return value ? value.label : null;
}

export function createSelectComponent(extractLabelFunction, overrideRenderValue = null) {
    return function ({layout, data, language, extractData, renderValue, className}) {
        if (overrideRenderValue) {
            renderValue = overrideRenderValue;
        }

        let extracted = extractLabelFunction(extractData(data, layout.name, language));

        return renderValue(layout, extracted, `data-type data-type--select ${className}`);
    }
}

export default createSelectComponent(extractSelectLabel);