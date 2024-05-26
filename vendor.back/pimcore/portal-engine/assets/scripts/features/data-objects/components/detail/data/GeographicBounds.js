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
import L from "leaflet";
import {Rectangle} from "react-leaflet";
import {basicPolyOptions, enabled, renderMapFromLayout} from "~portal-engine/scripts/utils/map";

export default function ({layout, data, language, extractData, renderValue, className}) {
    // content = buildMapsUrlForLatLng(extracted.NElatitude, extracted.NElongitude);
    const extracted = extractData(data, layout.name, language);
    let content = null;

    if (extracted && enabled() && extracted.NElatitude && extracted.NElongitude && extracted.SWlatitude && extracted.SWlongitude) {
        const bounds = L.latLngBounds(L.latLng(extracted.NElatitude, extracted.NElongitude), L.latLng(extracted.SWlatitude, extracted.SWlongitude));
        const center = [bounds.getCenter().lat, bounds.getCenter().lng];

        content = renderMapFromLayout(layout, center, bounds, (
            <Rectangle bounds={bounds} {...basicPolyOptions()}/>
        ));
    }

    return renderValue(layout, content, `data-type data-type--geographic-bounds ${className}`);
}