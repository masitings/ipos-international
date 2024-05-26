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
        content = (
            <div>
                <span className="d-inline-block position-relative mr-2" style={{top: "0.1em", height: "1em", width: "1em", backgroundColor: extracted}}></span>
                {extracted}
            </div>
        );
    }

    return renderValue(layout, content, `data-type data-type--color ${className}`);
}