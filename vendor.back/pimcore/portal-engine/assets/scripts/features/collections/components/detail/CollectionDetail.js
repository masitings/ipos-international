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
import SelectionBar from "~portal-engine/scripts/features/collections/components/detail/SelectionBar";
import ListView from "~portal-engine/scripts/features/collections/components/detail/ListView";
import Teaser from "~portal-engine/scripts/features/collections/components/detail/Teaser";

export default () => {
    return (
        <DataPoolList
            ListViewComponent={ListView}
            TeaserComponent={Teaser}
            SelectionBarComponent={SelectionBar}
        />
    )
}