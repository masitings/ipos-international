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
import Hotspots from "~portal-engine/scripts/components/image/Hotspots";
import Markers from "~portal-engine/scripts/components/image/Markers";
import {BasicImage, ImageWrapper} from "~portal-engine/scripts/features/data-objects/components/detail/data/Image";
import {extractStyle, extractLabel} from "~portal-engine/scripts/features/element/element-layout";

export default function ({layout, data, language, extractData, renderValue, className = ''}) {
    const extracted = extractData(data, layout.name, language, {});

    if (!extracted) {
        return renderValue(layout, null, className);
    }

    const hotspots = extracted.hotspots || [];
    const markers = extracted.marker || [];

    return (
        <ImageWrapper label={extractLabel(layout)} layout={layout} config={extracted} style={extractStyle(layout)} className={className}>
            <BasicHotspotImage src={extracted.thumbnail} hotspots={hotspots} markers={markers}/>
        </ImageWrapper>
    );
}

export function BasicHotspotImage({className, src, alt, title, style, hotspots, markers}) {
    return (
        <div className={`data-type data-type--hotspot-image ${className}`} style={style}>
            <div className="media__item data-object__hotspot-image d-flex justify-content-center">
                <div className="position-relative">
                    <BasicImage src={src} alt={alt} title={title}/>

                    <Hotspots hotspots={hotspots}/>
                    <Markers markers={markers}/>
                </div>
            </div>
        </div>
    );
}