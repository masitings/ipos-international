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

export default function ({layout, data, language, extractData, renderValue, className = ''}) {
    let extracted = extractData(data, layout.name, language);

    if (extracted) {
        if (extracted.href) {
            extracted = (
                <a href={extracted.href} className="text-primary" title={extracted.title} target={extracted.target} className={extracted.class}>
                    {extracted.text ? extracted.text : extracted.href}
                </a>
            );
        } else {
            extracted = null;
        }
    }

    return renderValue(layout, extracted, `data-type data-type--link ${className}`);
}