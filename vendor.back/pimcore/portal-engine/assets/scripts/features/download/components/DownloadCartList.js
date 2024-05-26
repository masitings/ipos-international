/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {FETCHING, NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import {
    getDownloadListFetchingStateByPageNumber, getDownloadListIdsByPageNumber,
    getDownloadListMessageByPageNumber, getDownloadListPageCount,
    getDownloadListPageNumber,
    getDownloadListPageSize, getDownloadListParamNames, getDownloadListParams, getDownloadListResultCount,
} from "~portal-engine/scripts/features/selectors";
import {connect} from "react-redux";
import React, {useEffect} from "react";
import Trans from "~portal-engine/scripts/components/Trans";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import DownloadCartListItem from "~portal-engine/scripts/features/download/components/DownloadCartListItem";
import DownloadConfigModal
    from "~portal-engine/scripts/features/download/components/DownloadConfig/DownloadConfigModal";
import DownloadPagination from "~portal-engine/scripts/features/download/components/DownloadPagination";
import {
    downloadCartClicked, requestListPage, setup, urlChanged
} from "~portal-engine/scripts/features/download/download-actions";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import {ReactComponent as DownloadIcon} from "~portal-engine/icons/arrow-alt-circle-down";
import {useSearchParams} from "~portal-engine/scripts/sliceHelper/list/list-components";
import DownloadClearCartButton from "~portal-engine/scripts/features/download/components/DownloadClearCartButton";
import {Alert} from "react-bootstrap";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import Media from "react-media";
import {getCartDownloadFetchingState} from "~portal-engine/scripts/features/download/download-selectors";
import CartDownloadMessageModal
    from "~portal-engine/scripts/features/download/components/DownloadConfig/CartDownloadMessageModal";

export function DownloadCart(props) {
    const {
        pageFetchingState,
        currentListParams = [],
        listParamNames = [],
        dispatch,
        isFetching = true,
        itemIds = [],
        pageCount,
        resultCount,
        pageFetchingMessage,
        isDownloading = false
    } = props;

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

    return (
        <Media query={MD_DOWN}>
            {matches => (
                <div className="main-content__main">
                    <div className="container">
                        {resultCount > 0 ? (
                            <div className="result-info">
                                <div className="row row-gutter--2 vertical-gutter vertical-gutter--3 align-items-center">
                                    <div className="d-none d-md-block col-12 col-md text-center text-md-left vertical-gutter__item">
                                        {resultCount} <Trans t={resultCount === 1 ? 'download.item': 'download.items'}/>
                                    </div>
                                    {resultCount > 0 ? (
                                        <div className="col-6 col-md-auto text-center vertical-gutter__item ml-md-auto">
                                            <DownloadClearCartButton block={matches}/>
                                        </div>
                                    ) : null}

                                    <div className="col-6 col-md-auto text-center vertical-gutter__item ml-md-auto">
                                        <ButtonWithIcon type="button"
                                                        variant="primary"
                                                        block={matches}
                                                        Icon={<DownloadIcon width="16" height="16"/>}
                                                        disabled={isDownloading}
                                                        isPulsing={isDownloading}
                                                        onClick={() => dispatch(downloadCartClicked())}>
                                            <Trans t={isDownloading ? 'download.loading' : 'download.cta'}/>
                                        </ButtonWithIcon>
                                    </div>
                                </div>
                            </div>
                        ) : null}

                        {pageFetchingMessage ? (
                            <div className="row justify-content-center my-4">
                                <div className="col-12 col-md-6">
                                    <Alert variant="danger">{pageFetchingMessage}</Alert>
                                </div>
                            </div>
                        ) : null}

                        {isFetching ? (
                            <div>
                                <LoadingIndicator className="my-4"/>
                            </div>
                        ) : (
                            resultCount === 0
                                ? (
                                    <div className="row justify-content-center my-4 my-lg-5">
                                        <div className="col-6">
                                            <div className="h3 text-center"><Trans t="download.empty-cart"/></div>
                                        </div>
                                    </div>
                                )
                                : (
                                    <ul className="list-unstyled vertical-gutter">
                                        {itemIds.map(id => (
                                            <li key={id} className="vertical-gutter__item">
                                                <DownloadCartListItem id={id}/>
                                            </li>
                                        ))}
                                    </ul>
                                )
                        )}

                        {pageCount > 1 ? (
                            <DownloadPagination/>
                        ) : null}

                        <CartDownloadMessageModal/>

                        <DownloadConfigModal/>
                    </div>
                </div>
            )}
        </Media>

    );
}

export const mapStateToProps = state => {
    const currentPageNumber = getDownloadListPageNumber(state);
    const currentPageFetchingState = getDownloadListFetchingStateByPageNumber(state, currentPageNumber);

    return {
        pageFetchingState: currentPageFetchingState,
        pageFetchingMessage: getDownloadListMessageByPageNumber(state, currentPageNumber),
        isFetching: currentPageFetchingState === FETCHING || currentPageFetchingState === NOT_ASKED,
        pageSize: getDownloadListPageSize(state),
        pageCount: getDownloadListPageCount(state),
        itemIds: getDownloadListIdsByPageNumber(state, currentPageNumber),
        resultCount: getDownloadListResultCount(state),
        currentListParams: getDownloadListParams(state),
        listParamNames: getDownloadListParamNames(state),
        isDownloading: getCartDownloadFetchingState(state.download) === FETCHING
    };
};

export default connect(mapStateToProps)(DownloadCart);