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

export default function ({layout, data, language, extractData, renderValue, className}) {
    const extracted = extractData(data, layout.name, language);
    let content = null;

    if (extracted) {
        content = `${extracted.value} ${extracted.unitAbbrevation ? extracted.unitAbbrevation : ""}`;
    }

    return renderValue(layout, content, `data-type data-type--quantity-value ${className}`);
}