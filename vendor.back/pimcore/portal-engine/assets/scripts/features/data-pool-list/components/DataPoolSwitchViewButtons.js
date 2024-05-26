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
import SwitchView, {defaultViews} from "~portal-engine/scripts/components/SwitchView";
import {connect} from "react-redux";
import {changeItemView} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {getCurrentView, getListViewAttributes} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import {LIST} from "~portal-engine/scripts/consts/teaser-list-views";

export function mapStateToProps(state) {
    let hasAttributes = getListViewAttributes(state).length;

    return {
        view: getCurrentView(state),
        views: hasAttributes
            ? defaultViews
            : defaultViews.filter(({id}) => id !== LIST)
    }
}

export const mapDispatchToProps = (dispatch, {}) => {
    return {
        onChangeView: (view) => dispatch(changeItemView(view))
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(SwitchView);