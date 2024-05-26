/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from 'react';
import Media from 'react-media';
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";

import FilterBar from "~portal-engine/scripts/components/filters/FilterBar";
import FilterOverlay from "~portal-engine/scripts/components/filters/FilterOverlay";

export default function (props) {
    return (
        <Media queries={{
            small: MD_DOWN,
        }}>
            {matches => (
                matches.small
                    ? <FilterOverlay {...props}/>
                    : <FilterBar {...props}/>
            )}
        </Media>
    );
}