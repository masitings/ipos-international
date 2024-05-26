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
import Trans from "~portal-engine/scripts/components/Trans";

export default function ({layout, data, language, extractData, renderValue, className}) {
    const extracted = extractData(data, layout.name, language);
    const consentGiven = extracted.consent;

    const translationKey = consentGiven ? "boolean.yes" : "boolean.no";
    const content = (<Trans t={translationKey}/>);

    return renderValue(layout, content, `data-type data-type--consent ${className}`);
}