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
import {getEmptyValue} from "~portal-engine/scripts/features/data-objects/components/detail/BasicValueRow";

function VerticalValueRow(props) {
    const {
        label,
        content,
        style
    } = props;

    let value = getEmptyValue();

    if(content) {
        value = content;
    }

    return (
        <div className="key-value-item" style={style}>
            <div className="key-value-item__label">
                {label}
            </div>
            <div className="key-value-item__value">
                {value}
            </div>
        </div>
    );
}

export default VerticalValueRow;

export function verticalRenderValue(layout, content) {
    return (<VerticalValueRow label={extractLabel(layout)} style={extractStyle(layout)} content={content}/>);
}