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

export default function ({layout, renderValue, className}) {
    return renderValue(layout, (
        <span dangerouslySetInnerHTML={{__html: "&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"}}></span>
    ), `data-type data-type--password ${className}`);
}