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
import {extractLabel, extractStyle} from "~portal-engine/scripts/features/element/element-layout";
import Trans from "~portal-engine/scripts/components/Trans";

export function getEmptyValue() {
    return (<Trans t="empty-data"/>);
}

function BasicValueRow(props) {
    const {
        label,
        content,
        style,
        className
    } = props;

    let value = getEmptyValue();

    if(content) {
        value = content;
    }

    return (
        <div className={`key-value-item ${className}`} style={style}>
            {label &&
            <div className="key-value-item__label">
                {label}
            </div>
            }

            <div className={`key-value-item__value`}>
                {value}
            </div>
        </div>
    );
}

export default BasicValueRow;

export function basicRenderValue(layout, content = null, className = "") {
    return (<BasicValueRow label={extractLabel(layout)} style={extractStyle(layout)} content={content} className={className}/>);
}