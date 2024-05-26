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
import Hotspot from "~portal-engine/scripts/components/image/Hotspot";

export default function ({hotspots}) {
    if (!Array.isArray(hotspots) || !hotspots.length) {
        return null;
    }

    return (
        <div className="image-hotspots">
            {hotspots.map((hotspot, i) => {
                return (
                    <Hotspot key={i} hotspot={hotspot}/>
                );
            })}
        </div>
    );
}