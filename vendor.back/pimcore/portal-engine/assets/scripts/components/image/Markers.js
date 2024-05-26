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
import Marker from "~portal-engine/scripts/components/image/Marker";

export default function ({markers}) {
    if (!Array.isArray(markers) || !markers.length) {
        return null;
    }

    return (
        <div className="image-markers">
            {markers.map((marker, i) => {
                return (
                    <Marker key={i} marker={marker}/>
                );
            })}
        </div>
    );
}