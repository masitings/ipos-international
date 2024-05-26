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
import True from "~portal-engine/icons/check-circle";
import False from "~portal-engine/icons/circle";

export default function ({layout, data, language, extractData, renderValue, className}) {
    let extracted = extractData(data, layout.name, language);

    if (typeof extracted === "number") {
        // every number greater than 0 is true
        extracted = extracted > 0;
    } else {
        // force cast to bool
        extracted = !!extracted;
    }

    const translationKey = extracted ? "boolean.yes" : "boolean.no";
    const icon = extracted ? (<True/>) : (<False/>);

    extracted = (
        <span>
            {icon} <Trans t={translationKey}/>
        </span>
    );

    return renderValue(layout, extracted, `data-type data-type--boolean ${className}`);
}