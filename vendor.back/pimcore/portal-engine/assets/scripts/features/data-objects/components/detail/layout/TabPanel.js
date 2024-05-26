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
    extractChildren,
    extractLabel,
    renderLayout,
    removeLabel,
    prepareChildAsTab,
    isInsideTab,
    extractStyle
} from "~portal-engine/scripts/features/element/element-layout";
import Tabs from "~portal-engine/scripts/components/tab/Tabs";
import Tab from "~portal-engine/scripts/components/tab/Tab";

export default function ({layout, data, language, extractData, renderValue, className = '', context = {}}) {
    const isSubTab = isInsideTab(context);

    return (
        <div className={`layout-type layout-type--tab-panel ${className}`} style={extractStyle(layout)}>
            <Tabs classNames={{wrapper: isSubTab ? '' : 'nav-level--1 nav-tabs--bar-sm'}}>
                {extractChildren(layout).map((child, i) => (
                    <Tab tab={i} key={i} label={extractLabel(child)}>
                        {renderLayout(removeLabel(child), data, language, extractData, renderValue, className, null, prepareChildAsTab(context))}
                    </Tab>
                ))}
            </Tabs>
        </div>
    );
}