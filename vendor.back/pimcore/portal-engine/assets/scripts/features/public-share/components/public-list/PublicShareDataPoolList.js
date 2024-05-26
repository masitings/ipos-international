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
import DataPoolList from '~portal-engine/scripts/features/data-pool-list/components/DataPoolList';
import Teaser from "~portal-engine/scripts/features/public-share/components/public-list/Teaser";
import SelectionBar from "~portal-engine/scripts/features/public-share/components/public-list/SelectionBar";
import ListView from "~portal-engine/scripts/features/public-share/components/public-list/ListView";

export default () => {
    return (
        <DataPoolList
            ListViewComponent={ListView}
            TeaserComponent={Teaser}
            SelectionBarComponent={SelectionBar}
        />
    )
}