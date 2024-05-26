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
import {noop} from "~portal-engine/scripts/utils/utils";
import {ReactComponent as GridIcon} from "~portal-engine/icons/th-large";
import {ReactComponent as ListIcon} from "~portal-engine/icons/th-list";
import {LIST, TILE} from "~portal-engine/scripts/consts/teaser-list-views";

export const defaultViews = [{
    id: TILE,
    Icon: GridIcon
}, {
    id: LIST,
    Icon: ListIcon
}];

export default function (props) {
    const {
        className = "",
        onChangeView = noop,
        view,
        views = defaultViews
    } = props;

    return views.length > 1 ? (
        <div className={className}>
            <div className={`btn-group btn-group-sm`} role="group">
                {views.map(({id, Icon}) => (
                    <button key={id} type="button"
                            className={`btn btn-outline-gray ${view === id ? 'active' : null}`}
                            onClick={() => onChangeView(id)}>
                        <Icon width="24" height="24"/>
                    </button>
                ))}
            </div>
        </div>
    ) : null;
}