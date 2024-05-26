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
import Card from "~portal-engine/scripts/components/Card";
import {renderChildren, extractStyle, extractLabel} from "~portal-engine/scripts/features/element/element-layout";

export default function ({layout, data, language, extractData, renderValue, className = '', context = {}}) {
    const content = renderChildren(layout, data, language, extractData, renderValue, 'vertical-gutter__item', context);

    if (layout.title) {
        return (
            <Card
                title={extractLabel(layout)}
                style={extractStyle(layout)}
                className={`layout-type layout-type--panel ${className}`}
                bodyClassName={"vertical-gutter"}
                collapsible={layout.collapsible}
                collapsed={layout.collapsed}
            >
                {content}
            </Card>
        );
    } else {
        return (
            <div className={`layout-type layout-type--panel layout-type--panel-empty ${className}`}>
                <div className={`vertical-gutter`} style={extractStyle(layout)}>
                    {content}
                </div>
            </div>
        );
    }
}