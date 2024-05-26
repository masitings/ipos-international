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
import {extractLabel} from "~portal-engine/scripts/features/element/element-layout";
import {ImageWrapper} from "~portal-engine/scripts/features/data-objects/components/detail/data/Image";

export function youtube(videoId) {
    return (
        <iframe src={`https://www.youtube.com/embed/${videoId}`} className="embed-responsive-item" frameBorder={0}
                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                allowFullScreen={true}></iframe>
    );
}

export function vimeo(videoId) {
    return (
        <iframe src={`https://player.vimeo.com/video/${videoId}`} className="embed-responsive-item" frameBorder={0}
                allow="autoplay; fullscreen" allowFullScreen={true}></iframe>
    );
}

export function dailymotion(videoId) {
    return (
        <iframe src={`https://www.dailymotion.com/embed/video/${videoId}`} className="embed-responsive-item"
                frameBorder={0} allowFullScreen={true}></iframe>
    );
}

export function asset(asset, poster, title, description, className = "embed-responsive-item") {
    return (
        <video className={className} controls poster={poster} title={title} allowFullScreen={true}>
            <source src={asset}/>
            <Trans t="video-not-supported"/>
        </video>
    );
}

export function video(data) {
    switch (data.type) {
        case "youtube":
            return youtube(data.data);

        case "vimeo":
            return vimeo(data.data);

        case "dailymotion":
            return dailymotion(data.data);

        case "asset":
            return asset(data.data, data.poster, data.title, data.description);

        default:
            return null;
    }
}

export default function ({layout, data, language, extractData, renderValue, className = null}) {
    const extracted = extractData(data, layout.name, language);

    if (!extracted) {
        return renderValue(layout, null, className);
    }

    return (
        <ImageWrapper className={className} config={extracted} layout={layout} label={extractLabel(layout)}>
            <div className="vertical-gutter__item media media--video data-type data-type--video">
                <div className="media__item">
                    <div className="embed-responsive embed-responsive-16by9">
                        {video(extracted)}
                    </div>
                </div>
            </div>
        </ImageWrapper>
    );
}