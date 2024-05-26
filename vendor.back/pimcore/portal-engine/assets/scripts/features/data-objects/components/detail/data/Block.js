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
import {extractStyle, renderChildren} from "~portal-engine/scripts/features/element/element-layout";

export default function ({layout, data, language, extractData, renderValue, className = ''}) {
    const extracted = extractData(data, layout.name, language);

    if (!extracted) {
        return renderValue(layout, null, className);
    }

    return (
        <div className={`data-type data-type--block ${className}`} style={extractStyle(layout)}>
            {extracted.map((blockData, i) => (
                <div className="data-type--block__item" key={i}>
                    <div className="vertical-gutter">
                        {renderChildren(layout, blockData.data, language, extractData, renderValue, 'vertical-gutter__item')}
                    </div>
                </div>
            ))}
        </div>
    );
}