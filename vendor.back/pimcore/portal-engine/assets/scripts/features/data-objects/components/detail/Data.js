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
import {connect} from "react-redux";
import {extractChildren, addChild, setLabel, renderLayout} from "~portal-engine/scripts/features/element/element-layout";
import {getCurrentLayout} from "~portal-engine/scripts/features/data-objects/object-layout";
import Versions from "~portal-engine/scripts/features/data-objects/components/detail/Versions";
import {
    getDetailData,
    getVersionsEnabled,
} from "~portal-engine/scripts/features/data-objects/data-object-selectors";

function injectVersionTab(layout) {
    if (layout.datatype === "layout" && layout.fieldtype === "tabpanel") {
        addChild(layout, "versions", "versions", () => (
            <Versions/>
        ));

        return true;
    }

    return false;
}

function injectVersionTabToExistingTabPanel(layout) {
    // ok so these scenarios should be handled here
    // 1. first layout item is a panel with a tab panel beneath
    // 2. first layout is a tab panel already

    if (injectVersionTab(layout)) {
        return true;
    }

    // try first layer
    let children = extractChildren(layout);
    let injected = false;

    children.forEach((child) => {
        if (!injected && injectVersionTab(child)) {
            injected = true;
        }
    });

    return injected;
}

export const mapStateToProps = state => ({
    data: getDetailData(state),
    versionsEnabled: getVersionsEnabled(state)
});

export function Data ({data, versionsEnabled}) {
    let layout = getCurrentLayout();

    if (typeof layout !== "object") {
        return null;
    }

    if (versionsEnabled) {
        if (!injectVersionTabToExistingTabPanel(layout)) {
            // no tabs available to inject the version tab into :/
            // create tab panel and wrap layout in it

            // force label for existing layout
            setLabel(layout, "data");

            // wrap it up!
            layout = {
                fieldtype: "tabpanel",
                datatype: "layout",
                childs: [layout]
            };

            injectVersionTab(layout);
        }
    }

    return renderLayout(layout, data);
}

export default connect(mapStateToProps)(Data);