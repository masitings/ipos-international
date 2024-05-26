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
import {getPageSize} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import DataPoolTeaser from "~portal-engine/scripts/features/data-pool-list/components/DataPoolTeaser";

export function DataPoolTileView({
    ids,
    pageSize = 60,
    TeaserComponent = DataPoolTeaser,
}) {
    return (
        <ul className="row list-unstyled vertical-gutter teaser-grid">
            {ids.map((id) => (
                <li className={`col-6 col-md vertical-gutter__item teaser-grid__item`}
                    key={id}>
                    <TeaserComponent id={id}/>
                </li>
            ))}

            {/* Placeholder for flexbox*/}
            {new Array(pageSize - (ids.length % pageSize)).fill(0).map((_, index) => (
                <li className={`col vertical-gutter__item teaser-grid__item`}
                    key={`placeholder-${index}`}/>
            ))}
        </ul>
    )
}

export const mapStateToProps = state => ({
    pageSize: getPageSize(state) || 60,
});

export default connect(mapStateToProps)(DataPoolTileView)