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
import {Marker, TileLayer, MapFromLayout, enabled} from "~portal-engine/scripts/utils/map";

export default function ({layout, data, language, extractData, renderValue, className}) {
    const extracted = extractData(data, layout.name, language);
    let content = null;

    if (extracted && enabled() && extracted.latitude && extracted.longitude) {
        const position = [extracted.latitude, extracted.longitude];

        content = (
            <MapFromLayout center={position} layout={layout}>
                <TileLayer/>
                <Marker position={position}/>
            </MapFromLayout>
        );
    }

    return renderValue(layout, content, `data-type data-type--geographic-point ${className}`);
}