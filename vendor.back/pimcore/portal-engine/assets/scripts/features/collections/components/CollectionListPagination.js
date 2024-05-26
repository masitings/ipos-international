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
import Pagination from "~portal-engine/scripts/components/Pagination";
import {getCurrentPageNumber, getPageCount} from "~portal-engine/scripts/features/collections/collections-selectors";
import {changePage} from "~portal-engine/scripts/features/collections/collections-actions";

export function mapStateToProps(state) {
    return {
        pageCount: getPageCount(state),
        currentPage: getCurrentPageNumber(state),
    }
}

export const mapDispatchToProps = {
    onPageClick: changePage,
};

export default connect(mapStateToProps, mapDispatchToProps)(Pagination)