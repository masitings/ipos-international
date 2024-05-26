/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useEffect} from "react";
import {NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import {requestTags} from "~portal-engine/scripts/features/tags/tags-actions";
import {getTagById, getTagsFetchingState} from "~portal-engine/scripts/features/selectors";
import {connect} from "react-redux";

export const mapStateToProps = (state, {ids = []}) => {
    const fetchingState = getTagsFetchingState(state);

    return ({
        fetchingState,
        ids,
        tags: fetchingState === SUCCESS ? ids.map(id => getTagById(state, id)) : []
    });
};

export const TagTextList = ({
    fetchingState,
    ids = [],
    tags = [],
    className = '',
    dispatch
}) => {
    useEffect(() => {
        if (fetchingState === NOT_ASKED && ids.length) {
            dispatch(requestTags(ids));
        }
    }, [ids]);

    return (
        <ul className={`list-unstyled ${className}`}>
            {tags.map(tag => tag.name).join(', ')}
        </ul>
    )
};

export default connect(mapStateToProps)(TagTextList)