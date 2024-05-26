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
import {extractStyle, extractLabel} from "~portal-engine/scripts/features/element/element-layout";
import {BasicHotspotImage} from "~portal-engine/scripts/features/data-objects/components/detail/data/HotspotImage";
import {ImageWrapper} from "~portal-engine/scripts/features/data-objects/components/detail/data/Image";
import Carousel from '@brainhubeu/react-carousel';

export default function ({layout, data, language, extractData, renderValue, className = ''}) {
    const extracted = extractData(data, layout.name, language);

    if (!extracted || extracted.length === 0) {
        return renderValue(layout, null, className);
    }

    const image = extracted[0];

    if(extracted.length === 1) {
        return (
            <ImageWrapper label={extractLabel(layout)} layout={layout} config={image} style={extractStyle(layout)} className={className}>
                <div className={`${className} data-type data-type--gallery-single media`}>
                    <div className="media__item">
                        <BasicHotspotImage src={image.thumbnail} hotspots={image.hotspots} markers={image.markers} className={className}/>
                    </div>
                </div>
            </ImageWrapper>
        );
    }

    return (
        <div className={`data-type data-type--gallery image-gallery ${className}`} style={extractStyle(layout)}>
            <ImageWrapper label={extractLabel(layout)} layout={layout} config={{downloadId: image.downloadId, dataPoolId: image.dataPoolId}} style={extractStyle(layout)} className={className}>
                <Carousel dots keepDirectionWhenDragging>
                    {extracted.map((image, i) => {
                        return image && (
                            <div key={i}>
                                <ImageWrapper layout={layout} config={{url: image.url}}>
                                    <BasicHotspotImage src={image.thumbnail} hotspots={image.hotspots} markers={image.markers}/>
                                </ImageWrapper>
                            </div>
                        );
                    })}
                </Carousel>
            </ImageWrapper>
        </div>
    );
}