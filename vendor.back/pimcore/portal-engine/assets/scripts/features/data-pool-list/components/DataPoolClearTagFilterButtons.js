/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment, useEffect} from "react";
import {requestTags} from "~portal-engine/scripts/features/tags/tags-actions";
import {connect} from "react-redux";
import {getTagById, getTagsFetchingState} from "~portal-engine/scripts/features/selectors";
import {getSelectedTagIds} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import {NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import ClearFilterButton from "~portal-engine/scripts/components/filter/clear/ClearFilterButton";
import {toggleTagSelection} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";

export const mapStateToProps = state => {
    const fetchingState = getTagsFetchingState(state);
    const ids = getSelectedTagIds(state);

    return ({
        fetchingState,
        ids,
        tags: fetchingState === SUCCESS ? ids.map(id => getTagById(state, id)) : []
    });
};

export function DataPoolClearTagFilterButtons({
    fetchingState,
    ids = [],
    tags = [],
    dispatch
}) {
    useEffect(() => {
        if (fetchingState === NOT_ASKED && ids.length) {
            dispatch(requestTags(ids));
        }
    }, [ids]);

    if (fetchingState !== SUCCESS) {
        return null;
    }

    return (
        <Fragment>
            {tags.map((tag) => (
                <ClearFilterButton key={`${tag.id}`}
                                   onClick={() => dispatch(toggleTagSelection({id: tag.id, state: false}))}
                                   className="vertical-gutter__item mr-2">
                    {tag.name}
                </ClearFilterButton>
            ))}
        </Fragment>

    )
}

export default connect(mapStateToProps)(DataPoolClearTagFilterButtons)