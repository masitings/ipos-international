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
import {connect} from "react-redux";
import {requestSidebarItems} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {getSidebarListFetchingState, getAllSidebarListIds, getSidebarActiveId} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import {FETCHING, NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import DataPoolTeaser from "~portal-engine/scripts/features/data-pool-list/components/DataPoolTeaser";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";

export const DataObjectList = (props) => {
    const {
        items = [],
        activeId,
        isLoading = false,
        activeIdRef = React.createRef(),
        listRef = React.createRef()
    } = props;

    useEffect(() => {
        if(activeIdRef.current) {
            listRef.current.parentNode.scrollTop = activeIdRef.current.offsetTop;
        }
    });

    return (
        isLoading ? (
            <LoadingIndicator className="my-4"/>
        ) : (
                <ul className="list-unstyled vertical-gutter" ref={listRef}>
                    {items.map((item, key) => {
                        return (
                            <li className="vertical-gutter__item" key={key} ref={item === activeId ? activeIdRef : null}>
                                <DataPoolTeaser readonly={true} size={"sm"} id={item} className={`teaser--shadow-sm ${activeId === item ? 'is-active' : null}`}/>
                            </li>
                        )
                    })}
                </ul>
            )
    );
};

// Add a wrapper to load the initial data
export default connect(
    (state) => ({
        fetchingState: getSidebarListFetchingState(state),
        items: getAllSidebarListIds(state),
        activeId: getSidebarActiveId(state)
    })
)(({fetchingState, dispatch, ...props}) => {
    useEffect(function () {
        if (fetchingState === NOT_ASKED) {
            dispatch(requestSidebarItems());
        }
    }, []);

    let isLoading = true;
    if (fetchingState === SUCCESS) {
        isLoading = false;
    }


    let transformedProps = {
        ...props,
        isLoading
    };

    if (fetchingState === NOT_ASKED || fetchingState === FETCHING) {
        transformedProps.isLoading = true;
    }

    return (<DataObjectList {...transformedProps}/>)
});