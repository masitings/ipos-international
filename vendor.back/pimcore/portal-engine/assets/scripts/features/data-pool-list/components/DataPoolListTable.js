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
import DataTable from "~portal-engine/scripts/components/DataTable";
import {connect} from "react-redux";
import {
    getItemById,
    getListViewAttributes
} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import DataPoolListTableRow from "~portal-engine/scripts/features/data-pool-list/components/DataPoolListTableRow";

export const mapStateToProps = (state, {items}) => ({
    items: items.map((id) => getItemById(state, id)),
    listViewAttributes: getListViewAttributes(state),
});

export default connect(mapStateToProps)((props) => (
    <DataTable {...props} RowComponent={DataPoolListTableRow}/>
));