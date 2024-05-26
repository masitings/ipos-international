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
import {connect} from "react-redux";
import Filters from "~portal-engine/scripts/components/filters/Filters";
import {changeFilter} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {getAllVisibleFilters, getFilterStateByName} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import * as filtersByType from "~portal-engine/scripts/components/filter/inputs/index";

export function mapStateToProps(state) {
    return {
        componentsByType: filtersByType,
        filters: getAllVisibleFilters(state)
            .map(filter => ({
                ...filter,
                ...getFilterStateByName(state, filter.name)
            }))
    }
}

export const mapDispatchToProps = {
    onChange: changeFilter
};

export default connect(mapStateToProps, mapDispatchToProps)(Filters)