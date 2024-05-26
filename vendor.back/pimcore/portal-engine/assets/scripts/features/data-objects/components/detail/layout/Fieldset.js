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
import {renderChildren, extractStyle, extractLabel} from "~portal-engine/scripts/features/element/element-layout";
import Fieldset from "~portal-engine/scripts/components/Fieldset"

export default function ({layout, data, language, extractData, renderValue, className = '', context = {}}) {
    const content = renderChildren(layout, data, language, extractData, renderValue, 'vertical-gutter__item', context);

    return (
        <Fieldset style={extractStyle(layout)} title={extractLabel(layout)} className={`layout-type layout-type--fieldset ${className}`}>
            <div className="vertical-gutter">
                {content}
            </div>
        </Fieldset>
    );
}