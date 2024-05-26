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
import {BasicImage} from "~portal-engine/scripts/features/data-objects/components/detail/data/Image";

export default function ({layout, data, language, extractData, renderValue, className = ''}) {
    let extracted = extractData(data, layout.name, language);
    let label = extractLabel(layout);

    if(!extracted) {
        return renderValue(layout, null, className);
    }

    const style = {
        width: layout.previewWidth ? `${layout.previewWidth}px` : null,
        height: layout.previewHeight ? `${layout.previewHeight}px` : null,
        ...extractStyle(layout)
    };

    return (
        <div className={`media data-type data-type--external-image ${className}`}>
            {label ? (
                <div className="media__label">{label}</div>
            ) : null}

            <div className="media__item text-center">
                <BasicImage src={extracted} alt={extracted} title={extracted} style={style}/>
            </div>
        </div>
    );
}