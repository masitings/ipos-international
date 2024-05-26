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
import {extractChildren, renderLayout, renderChildren, extractStyle} from "~portal-engine/scripts/features/element/element-layout";
import {verticalRenderValue} from "~portal-engine/scripts/features/data-objects/components/detail/VerticalValueRow";

export default function ({layout, data, language, extractData, renderValue, className = '', context = {}}) {
    const children = extractChildren(layout);

    if(layout.layout !== "vbox") {
        return renderChildren(layout, data, language, extractData, renderValue, className, context);
    }

    return (
        <div className={`layout-type layout-type--field-container ${className}`} style={extractStyle(layout)}>
            <div className="layout-type--field-container__row row">
                {children.map((child, i) => {
                    return (
                        <div className="col layout-type--field-container__col" key={i}>
                            {renderLayout(child, data, language, extractData, verticalRenderValue, null, context)}
                        </div>
                    );
                })}
            </div>
        </div>
    );
}