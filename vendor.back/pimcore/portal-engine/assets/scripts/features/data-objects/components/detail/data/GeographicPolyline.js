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
import {Polyline} from "react-leaflet"
import {enabled, calculatePolyInformationFromLayoutData, renderMapFromLayout, basicPolyOptions} from "~portal-engine/scripts/utils/map";

export default function ({layout, data, language, extractData, renderValue, className}) {
    const extracted = extractData(data, layout.name, language);
    let content = null;

    if (extracted && enabled() && extracted.length) {
        const {positions, center, bounds} = calculatePolyInformationFromLayoutData(extracted);

        content = renderMapFromLayout(layout, center, bounds, (
            <Polyline positions={positions} {...basicPolyOptions()}/>
        ));
    }

    return renderValue(layout, content, `data-type data-type--geographic-polyline ${className}`);
}