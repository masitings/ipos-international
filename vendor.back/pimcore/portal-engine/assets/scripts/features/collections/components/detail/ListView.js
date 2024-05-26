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
import {mapStateToProps} from "~portal-engine/scripts/features/data-pool-list/components/DataPoolListTable";
import ListViewItem from "~portal-engine/scripts/features/collections/components/detail/ListViewItem";

export default connect(mapStateToProps)((props) => (
    <DataTable {...props} RowComponent={ListViewItem}/>
));