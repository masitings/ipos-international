/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import Dropdown from "~portal-engine/scripts/components/Dropdown";
import {connect} from "react-redux";
import {setCurrentOrderBy} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {getCurrentOrderBy, getOrderByOptions} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";

export const mapStateToProps = state => {
    return {
        items: getOrderByOptions(state),
        activeItem: getCurrentOrderBy(state)
    }
};

export const mapDispatchToProps = (dispatch, ownProps) => ({
    onDropdownClickItem: (item) => dispatch(setCurrentOrderBy(item.value))
});

export default connect(mapStateToProps, mapDispatchToProps)(Dropdown)