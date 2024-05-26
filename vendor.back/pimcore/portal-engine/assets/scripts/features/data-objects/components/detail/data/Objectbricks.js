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
import Tabs from "~portal-engine/scripts/components/tab/Tabs"
import Tab from "~portal-engine/scripts/components/tab/Tab"
import Trans from "~portal-engine/scripts/components/Trans"
import {extractStyle, prepareChildAsTab, renderLayout} from "~portal-engine/scripts/features/element/element-layout";
import {getObjectbrickLayoutDefinition} from "~portal-engine/scripts/features/data-objects/object-layout";

export function Objectbrick({type, data, language, extractData, renderValue, className = '', context = {}}) {
    const definition = getObjectbrickLayoutDefinition(type);

    if (!definition) {
        return null;
    }

    return (
        <div className={`data-type--objectbricks__objectbrick ${className}`}>
            <div className="vertical-gutter">
                {renderLayout(definition, data, language, extractData, renderValue, className, prepareChildAsTab(context))}
            </div>
        </div>
    );
}

export default function ({layout, data, language, extractData, renderValue, className = '', context = {}}) {
    let extracted = extractData(data, layout.name, language);

    if (!extracted) {
        return renderValue(layout, null, className);
    }

    extracted = extracted.filter(Boolean);

    if (!extracted.length) {
        return renderValue(layout, null, className);
    }

    return (
        <div className={`data-type data-type--objectbricks ${className}`} style={extractStyle(layout)}>
            <Tabs>
                {extracted.map((item) => (
                    <Tab tab={item.type} key={item.type} label={(<Trans t={item.type} domain="domain-object"/>)}>
                        <Objectbrick
                            type={item.type}
                            data={item.data}
                            language={language}
                            extractData={extractData}
                            renderValue={renderValue}
                            className={className}
                            context={prepareChildAsTab(context)}
                        />
                    </Tab>
                ))}
            </Tabs>
        </div>
    );
}