/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useEffect} from 'react';
import {connect} from "react-redux";
import {getChildTagIdsById, getTagsFetchingState} from "~portal-engine/scripts/features/selectors";
import {requestTags} from "~portal-engine/scripts/features/tags/tags-actions";
import {FETCHING, NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import TagTreeNode from "~portal-engine/scripts/features/tags/coponents/TagTreeNode";
import {noop} from "~portal-engine/scripts/utils/utils";

export const mapStateToProps = (state, {parentId = null}) => {
    return ({
        ids: getChildTagIdsById(state, parentId)
    });
};

export const Tree = connect(mapStateToProps)(props => {
    const {
        ids = [],
        selectedIds = [],
        level = 0,
        onSelectionChange = noop
    } = props;

    return (
        <ol className={`list-unstyled tree tree--level-${level}`}>
            {ids.map((id) => {
                return <li className={`tree__item`} key={id}>
                    <TagTreeNode id={id}
                                 level={level}
                                 selectedIds={selectedIds}
                                 onSelectionChange={onSelectionChange}
                                 SubTreeComponent={Tree}
                    />
                </li>
            })}
        </ol>
    )
});


// Add a wrapper to load the initial data
export default connect(
    (state) => ({
        fetchingState: getTagsFetchingState(state)
    })
)(({fetchingState, dispatch, ...props}) => {
    useEffect(function () {
        if (fetchingState === NOT_ASKED) {
            dispatch(requestTags(props.selectedIds));
        }
    }, []);

    return (fetchingState === NOT_ASKED || fetchingState === FETCHING)
        ? <LoadingIndicator className="mt-3" size="sm" showText={false}/>
        : <Tree {...props}/>
});

