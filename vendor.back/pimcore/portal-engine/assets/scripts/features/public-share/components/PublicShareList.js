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
import {connect} from "react-redux";
import Trans from "~portal-engine/scripts/components/Trans";
import {useSearchParams} from "~portal-engine/scripts/sliceHelper/list/list-components";
import {FETCHING, NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import {requestListPage, setup, urlChanged} from "~portal-engine/scripts/features/public-share/public-share-actions";
import {
    getCurrentPageNumber,
    getFetchingMessageByPage,
    getFetchingStateByPage,
    getIdsByPageNumber,
    getListParamNames,
    getListParams,
    getPageCount,
    getPageSize,
    getResultCount,
} from "~portal-engine/scripts/features/public-share/public-share-selectors";
import PublicShareListPagination from "~portal-engine/scripts/features/public-share/components/PublicShareListPagination";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import PublicShareListItem from "~portal-engine/scripts/features/public-share/components/PublicShareListItem";

export function PublicShareList({
    ids = [],
    isFetching = true,
    pageFetchingState,
    currentListParams,
    listParamNames,
    pageCount = 0,
    resultCount = 0,
    dispatch
}) {
    // Connect list state with url search params
    useSearchParams({
        currentParams: currentListParams,
        paramNames: listParamNames,
        onSetup: urlSearchParams => dispatch(setup(urlSearchParams)),
        onURLChanged: urlSearchParams => dispatch(urlChanged(urlSearchParams)),
    });

    // fetch required data when needed
    useEffect(() => {
        if (pageFetchingState === NOT_ASKED) {
            dispatch(requestListPage());
        }
    }, [pageFetchingState]);


    return isFetching ? (
        <LoadingIndicator className="my-4"/>
    ) : (
        resultCount === 0 ? (
            <div className="row justify-content-center my-4 my-lg-5">
                <div className="col-6">
                    <div className="h3 text-center"><Trans t="public-share.no-results"/></div>
                </div>
            </div>
        ) : (
            <Fragment>
                <h1><Trans t="public-share.list.headline"/></h1>
                <div className="data-table-container">
                    <div className="data-table table-responsive">
                        <table className="table">
                            <thead>
                            <tr>
                                <th><Trans t="public-share.list.name"/></th>
                                <th><Trans t="public-share.list.item-count"/></th>
                                <th><Trans t="public-share.list.expire-date"/></th>
                                <th><Trans t="public-share.list.show-terms-text"/></th>
                                <th><Trans t="public-share.list.terms-text"/></th>
                                <th><span className="sr-only"><Trans t="public-share.list.actions"/></span></th>
                            </tr>
                            </thead>
                            <tbody>
                            {ids.map((id) => (
                                <PublicShareListItem key={id} id={id}/>
                            ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                {pageCount > 1 ? (
                    <PublicShareListPagination/>
                ) : null}
            </Fragment>
        )
    );

}

export const mapStateToProps = state => {
    const currentPageNumber = getCurrentPageNumber(state);
    const currentPageFetchingState = getFetchingStateByPage(state, currentPageNumber);

    return {
        pageFetchingState: currentPageFetchingState,
        pageFetchingMessage: getFetchingMessageByPage(state, currentPageNumber),
        isFetching: currentPageFetchingState === FETCHING || currentPageFetchingState === NOT_ASKED,
        pageSize: getPageSize(state),
        pageCount: getPageCount(state),
        ids: getIdsByPageNumber(state, currentPageNumber),
        currentListParams: getListParams(state),
        listParamNames: getListParamNames(state),
        resultCount: getResultCount(state),
    };
};


export default connect(mapStateToProps)(PublicShareList);