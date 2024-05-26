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
import {renderChildren, extractStyle} from "~portal-engine/scripts/features/element/element-layout";

export default function ({layout, data, language, extractData, renderValue, className = '', context = {}}) {
    return (
        <div className={`layout-type layout-type--text alert alert-light ${className}`} style={extractStyle(layout)}>
            {layout.html && <span dangerouslySetInnerHTML={{__html: layout.html}}></span>}

            {renderChildren(layout, data, language, extractData, renderValue, null, context)}
        </div>
    );
}