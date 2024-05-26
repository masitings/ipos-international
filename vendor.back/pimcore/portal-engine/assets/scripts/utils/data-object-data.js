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
import Trans from "~portal-engine/scripts/components/Trans";
import BasicValueRow from "~portal-engine/scripts/features/data-objects/components/detail/BasicValueRow";

export function formatHotspotImageData(data) {
    if (!Array.isArray(data) || !data.length) {
        return null;
    }

    return data.map((item, i) => {
        return (
            <BasicValueRow key={i} label={(<Trans t={item.name} domain="data-object"/>)} content={item.value}/>
        );
    });
}

export function toCamelCase(string) {
    return string.replace(/^([A-Z])|[\s-](\w)/g, function (match, p1, p2) {
        if (p2) return p2.toUpperCase();
        return p1.toLowerCase();
    });
}

export function convertCssToObject(css) {
    if (!css) {
        return {};
    }

    css = css.replace(/([\w-.]+)\s*[^;]+\);?/g, '$1:$2,');
    css = css.replace(/,+$/, '');
    css = css.replace(/;+$/, '');

    let properties = css.split(', ');
    let cssObject = {};

    properties.forEach(function (property) {
        let cssProp = property.split(':');
        let cssKey = toCamelCase(cssProp[0]);

        cssObject[cssKey] = cssProp[1].trim();
    });

    return cssObject;
}