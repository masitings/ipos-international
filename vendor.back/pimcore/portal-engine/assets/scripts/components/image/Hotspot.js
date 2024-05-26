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
import Popover from "~portal-engine/scripts/components/Popover";
import {formatHotspotImageData} from "~portal-engine/scripts/utils/data-object-data";

export default function Hotspot({hotspot}) {
    const content = formatHotspotImageData(hotspot.data);

    return (
        content ? (
            <Popover content={content}>
                <button type="button" className="btn btn-link image-hotspot" style={{top: `${hotspot.top}%`, left: `${hotspot.left}%`, width: `${hotspot.width}%`, height: `${hotspot.height}%`}}/>
            </Popover>
        ): <button type="button" className="btn btn-link image-hotspot" style={{top: `${hotspot.top}%`, left: `${hotspot.left}%`, width: `${hotspot.width}%`, height: `${hotspot.height}%`}}/>
    );
}