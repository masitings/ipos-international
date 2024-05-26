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
import {connect} from "react-redux";
import {getLanguage} from "~portal-engine/scripts/utils/intl";
import {noop} from "~portal-engine/scripts/utils/utils";
import {getSelectedVersionIds} from "~portal-engine/scripts/features/data-objects/data-object-selectors";
import {toggleVersionSelection} from "~portal-engine/scripts/features/data-objects/data-object-actions";

export const mapStateToProps = (state, {version}) => ({
    isSelected: !!getSelectedVersionIds(state).find(id => id === version.id)
});

export const mapDispatchToProps = (dispatch, {version}) => ({
    toggleSelection: (isSelected) => {
        dispatch(toggleVersionSelection(version.id, !isSelected));
    }
});

const mergeProps = (stateProps, dispatchProps, props) => ({
    ...props,
    ...stateProps,
    ...dispatchProps,
    toggleSelection: (event) => {
        event.preventDefault();
        event.stopPropagation();

        dispatchProps.toggleSelection(stateProps.isSelected);
    }
});

export function VersionHistoryRow({isSelected, version, toggleSelection}) {
    const language = getLanguage();

    return (
        <tr onClick={toggleSelection}>
            <td width="20%">{version.id}</td>
            <td width="40%">{version.note}</td>
            <td width="20%">{version.date}</td>
            <td width="20%" className="text-right">
                <input type="checkbox" checked={isSelected} onChange={noop} onClick={toggleSelection}
                       style={{pointerEvents: "none"}}/>
            </td>
        </tr>
    );
}

export default connect(mapStateToProps, mapDispatchToProps, mergeProps)(VersionHistoryRow);