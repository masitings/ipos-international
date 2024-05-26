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
import {LAYOUT_WIDTH} from "~portal-engine/scripts/consts/layout";
import {
    renderLayout,
    extractChildren,
    extractStyle,
    extractRegion,
    extractWidth
} from "~portal-engine/scripts/features/element/element-layout";

function calculateRelativeWidthByGrid(sumWidth, width) {
    return Math.floor((LAYOUT_WIDTH * width) / sumWidth);
}

function renderChildren(children, data, language, extractData, renderValue, childrenClassName = '', context = {}, useRelativeWidth = false, sumWidth = null) {
    return (
        <div className={`${childrenClassName}`}>
            <div className={`row layout-type--region-panel__row vertical-gutter`}>
            {children.map((child, i) => {
                const width = extractWidth(child);
                const style = {};

                // by default use max a third of the space
                let colSize = Math.max(Math.floor(LAYOUT_WIDTH / 3), LAYOUT_WIDTH / children.length);

                if (useRelativeWidth && sumWidth) {
                    colSize = calculateRelativeWidthByGrid(sumWidth, width);
                }

                let className = `col-md-${colSize}`;

                if (!useRelativeWidth && sumWidth) {
                    className = `col-md`

                    if(width) {
                        style.maxWidth = `${width}px`;
                    }
                }

                return (
                    <div className={`col-${LAYOUT_WIDTH} ${className} layout-type--region-panel__col ${childrenClassName}`} style={style} key={i}>
                        {renderLayout(child, data, language, extractData, renderValue, null, null, context)}
                    </div>
                );
            })}
        </div>
        </div>
    );
}

export default function ({layout, data, language, extractData, renderValue, className = '', context = {}}) {
    const children = extractChildren(layout);

    const topChildren = [];
    const bottomChildren = [];
    const leftChildren = [];
    const rightChildren = [];
    let centerChildren = [];
    let useRelativeWidth = true;
    let sumWidth = 0;

    children.forEach((child) => {
        let width = extractWidth(child);
        let region = extractRegion(child);

        if (region === "north") {
            topChildren.push(child);
        } else if (region === "south") {
            bottomChildren.push(child);
        } else {
            if(region === "west") {
                leftChildren.push(child);
            } else if ( region === "east") {
                rightChildren.push(child);
            } else {
                centerChildren.push(child);
            }

            sumWidth += width;

            if (!width) {
                useRelativeWidth = false;
            }
        }
    });

    centerChildren = leftChildren.concat(centerChildren).concat(rightChildren);

    return (
        <div className={`layout-type layout-type--region-panel ${className}`} style={extractStyle(layout)}>
            <div className="vertical-gutter">
                {renderChildren(topChildren, data, language, extractData, renderValue, className, context)}

                {renderChildren(centerChildren, data, language, extractData, renderValue, className, context, useRelativeWidth, sumWidth)}

                {renderChildren(bottomChildren, data, language, extractData, renderValue, className, context)}
            </div>
        </div>
    );
}