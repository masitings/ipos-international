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
import {extractSelectLabel} from "~portal-engine/scripts/features/data-objects/components/detail/data/Select";

export function commaSeparateValues(values) {
    if (values && Array.isArray(values)) {
        return values.join(", ");
    }

    return values;
}

export function createMultiSelectComponent(extractLabelFunction, displayValuesFunction, overrideRenderValue = null) {
    return function ({layout, data, language, extractData, renderValue, className}) {
        if (overrideRenderValue) {
            renderValue = overrideRenderValue;
        }

        let extracted = extractData(data, layout.name, language);

        if (extracted && Array.isArray(extracted)) {
            extracted = displayValuesFunction(extracted.map(extractLabelFunction));
        }

        return renderValue(layout, extracted, `data-type data-type--multi-select ${className}`);
    }
}

export default createMultiSelectComponent(extractSelectLabel, commaSeparateValues);