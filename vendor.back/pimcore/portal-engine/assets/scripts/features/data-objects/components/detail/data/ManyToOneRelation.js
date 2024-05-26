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
    let content = null;

    if (extracted) {
        content = extracted.name || extracted.path;

        if (extracted.url) {
            content = (<a href={extracted.url} className="text-primary" title={content}>{content}</a>)
        }
    }

    return renderValue(layout, content, `data-type data-type--many-to-one ${className}`);
}