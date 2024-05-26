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
    let extracted = extractData(data, layout.name, language);
    let content = null;

    if (extracted) {
        content = (<div className="wysiwyg" dangerouslySetInnerHTML={{__html: extracted}}></div>);
    }

    return renderValue(layout, content, `data-type data-type--wysiwyg ${className}`);
}