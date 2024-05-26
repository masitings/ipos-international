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
import {
    extractStyle,
    renderLayout
} from "~portal-engine/scripts/features/element/element-layout";
import {
    getFieldcollectionLayoutDefinition
} from "~portal-engine/scripts/features/data-objects/object-layout";

export function Fieldcollection({type, data, language, extractData, renderValue, context}) {
    const definition = getFieldcollectionLayoutDefinition(type);

    if (!definition) {
        return null;
    }

    return renderLayout(definition, data, language, extractData, renderValue, null, context);
}

export default function ({layout, data, language, extractData, renderValue, className = '', context = {}}) {
    const extracted = extractData(data, layout.name, language);

    if (!extracted || !extracted.length) {
        return renderValue(layout, null, className);
    }

    return (
        <div className={`data-type data-type--fieldcollection vertical-gutter ${className}`} style={extractStyle(layout)}>
            {extracted.map((fieldcollectionData, i) => (
                <div className="data-type--fieldcollection__item vertical-gutter__item" key={i}>
                    <Fieldcollection
                        type={fieldcollectionData.type}
                        data={fieldcollectionData.data}
                        language={language}
                        extractData={extractData}
                        renderValue={renderValue}
                        context={context}
                    />
                </div>
            ))}
        </div>
    );
}
