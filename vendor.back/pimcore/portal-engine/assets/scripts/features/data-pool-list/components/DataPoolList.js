/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {FETCHING, NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import {connect} from "react-redux";
import React, {Fragment, useEffect} from "react";
import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import {
    requestListPage,
    requestCurrentFilterState,
    urlChanged,
    setup
} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {
    getCurrentPageNumber, getCurrentView,
    getFetchingMessageByPage,
    getFetchingStateByPage,
    getFilterFetchingState,
    getFilterStatesFetchingState,
    getIdsByPageNumber, getListParamNames, getListParams,
    getPageCount,
    getPageSize, getResultCount,
    getSelectedFilterValues
} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import SidebarLayout from "~portal-engine/scripts/components/layouts/SidebarLayout";
import {ReactComponent as Bars} from "~portal-engine/icons/bars";
import Alert from "react-bootstrap/Alert";
import DownloadConfigModal from "~portal-engine/scripts/features/download/components/DownloadConfig/DownloadConfigModal";
import DataPoolFilter from "~portal-engine/scripts/features/data-pool-list/components/DataPoolFilter";
import DataPoolClearFilterButtons from "~portal-engine/scripts/features/data-pool-list/components/DataPoolClearFilterButtons";
import DataPoolTeaser from "~portal-engine/scripts/features/data-pool-list/components/DataPoolTeaser";
import DataPoolOrderByDropdown from "~portal-engine/scripts/features/data-pool-list/components/DataPoolOrderByDropdown";
import DataPoolSwitchViewButtons from "~portal-engine/scripts/features/data-pool-list/components/DataPoolSwitchViewButtons";
import DataPoolPagination from "~portal-engine/scripts/features/data-pool-list/components/DataPoolPagination";
import {getConfig} from "~portal-engine/scripts/utils/general";
import DataPoolListNavigation from "~portal-engine/scripts/features/data-pool-list/components/DataPoolListNavigation";
import DataPoolSelectionBar from "~portal-engine/scripts/features/data-pool-list/components/DataPoolSelectionBar";
import DataPoolListTable from "~portal-engine/scripts/features/data-pool-list/components/DataPoolListTable";
import {useSearchParams} from "~portal-engine/scripts/sliceHelper/list/list-components";
import AddToCollectionModal from "~portal-engine/scripts/features/collections/components/AddToCollectionModal";
import MultiDownloadMessageModal
    from "~portal-engine/scripts/features/download/components/DownloadConfig/MultiDownloadMessageModal";
import {DataPoolTileView} from "~portal-engine/scripts/features/data-pool-list/components/DataPoolTileView";
import DataPoolUpdateModal from "~portal-engine/scripts/features/data-pool-list/components/DataPoolUpdateModal";
import EditMetaDataModal from "~portal-engine/scripts/features/assets/components/AssetEditMetaDataModal";
import CreatePublicShareModal from "~portal-engine/scripts/features/public-share/components/CreatePublicShareModal";
import DataPoolSelectAll from "~portal-engine/scripts/features/data-pool-list/components/DataPoolSelectAll";

const SHOW_ALL_COUNT_MAX = getConfig('list.selectAllMaxSize');

export function DataPoolList(props) {
    // default components
    props = {
        DesktopComponent: DesktopList,
        MobileComponent: MobileList,
        EmptyResultComponent: EmptyResult,
        TeaserComponent: DataPoolTeaser,
        FiltersComponent: DataPoolFilter,
        ClearFilterButtonsComponent: DataPoolClearFilterButtons,
        SelectionBarComponent: DataPoolSelectionBar,
        ListViewComponent: DataPoolListTable,
        OrderByDropdownComponent: DataPoolOrderByDropdown,
        DataPoolSelectAllComponent: DataPoolSelectAll,
        SwitchViewButtonsComponent: DataPoolSwitchViewButtons,
        PaginationComponent: DataPoolPagination,
        ListNavigationComponent: DataPoolListNavigation,
        AddToCollectionModalComponent: AddToCollectionModal,
        DownloadConfigModalComponent: DownloadConfigModal,
        DataPoolUpdateModalComponent: DataPoolUpdateModal,
        MultiDownloadMessageModalComponent: MultiDownloadMessageModal,
        CreatePublicShareModalComponent: CreatePublicShareModal,
        ...props
    };

    const {
        DesktopComponent,
        MobileComponent,
        pageFetchingState,
        filterFetchingState,
        filterValuesFetchingState,
        currentListParams = [],
        listParamNames = [],
        dispatch,
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
        if (filterFetchingState === SUCCESS && pageFetchingState === NOT_ASKED) {
            dispatch(requestListPage());
        }
    }, [pageFetchingState, filterFetchingState]);

    useEffect(() => {
        if (filterFetchingState === SUCCESS && filterValuesFetchingState === NOT_ASKED) {
            dispatch(requestCurrentFilterState());
        }
    }, [filterValuesFetchingState, filterFetchingState]);


    return (
        <Media queries={{
            small: MD_DOWN,
        }}>
            {matches => (
                matches.small
                    ? <MobileComponent {...props}/>
                    : <DesktopComponent {...props}/>
            )}
        </Media>
    );
}

export function MobileList(props) {
    const {
        SelectionBarComponent,
        FiltersComponent,
        ListNavigationComponent
    } = props;

    return (
        <Fragment>
            <div className="container full-height-layout__fit mb-3">
                <FiltersComponent subTitle={props.hasNavigation
                    ? <Trans t="filter.overlay-filter-title"/>
                    : null
                }>
                    {props.hasNavigation ? (
                        <section className="my-4">
                            <h5><Trans t="filter.overlay-navigation-title"/></h5>

                            <ListNavigationComponent/>
                        </section>
                    ) : null}
                </FiltersComponent>
            </div>

            <div className="container full-height-layout__fill main-content__main">
                <List {...props} forceTileView={true}/>
            </div>

            <SelectionBarComponent className="full-height-layout__fit"/>
        </Fragment>

    );
}

export function DesktopList(props) {
    const {
        SelectionBarComponent,
        FiltersComponent,
        ListNavigationComponent
    } = props;

    const sidebar = (
        <ListNavigationComponent/>
    );

    return (
        <Fragment>
            <FiltersComponent className="full-height-layout__fit"/>

            {props.hasNavigation ? (
                <SidebarLayout className="full-height-layout__fill" sidebarChildren={sidebar}>
                    <List {...props}/>
                </SidebarLayout>
            ) : (
                <div className="main-content__main full-height-layout__fill">
                    <div className="container">
                        <List {...props}/>
                    </div>
                </div>
            )}

            <SelectionBarComponent className="full-height-layout__fit"/>
        </Fragment>
    );
}

export function EmptyResult() {
    return (
        <div className="row justify-content-center my-4 my-lg-5">
            <div className="col-6">
                <div className="h3 text-center"><Trans t="listing.no-results"/></div>
            </div>
        </div>
    );
}

export function List(props) {
    const {
        isFetching = true,
        itemIds = [],
        pageCount,
        view,
        resultCount,
        forceTileView = false,
        pageFetchingMessage,
        TeaserComponent = DataPoolTeaser,
        ClearFilterButtonsComponent = DataPoolClearFilterButtons,
        TileViewComponent = DataPoolTileView,
        ListViewComponent = DataPoolListTable,
        OrderByDropdownComponent = DataPoolOrderByDropdown,
        DataPoolSelectAllComponent = DataPoolSelectAll,
        SwitchViewButtonsComponent = DataPoolSwitchViewButtons,
        PaginationComponent = DataPoolPagination,
        AddToCollectionModalComponent = AddToCollectionModal,
        DownloadConfigModalComponent = DownloadConfigModal,
        MultiDownloadMessageModalComponent = MultiDownloadMessageModal,
        DataPoolUpdateModalComponent = DataPoolUpdateModal,
        EditMetaDataModalComponent = EditMetaDataModal,
        CreatePublicShareModalComponent = CreatePublicShareModal,
        EmptyResultComponent,
    } = props;

    let sortByName = useTranslation('listing.sort-by');

    return (
        <Fragment>
            <div className="mb-3">
                <div className="row vertical-gutter vertical-gutter--3">
                    <div className="col-md-6 vertical-gutter__item">
                        <ClearFilterButtonsComponent/>
                    </div>

                    <div className="col-md-6 vertical-gutter__item ml-auto">
                        <div className="row vertical-gutter vertical-gutter--3 justify-content-between justify-content-md-end align-items-center">
                            {resultCount > 0 ? (
                                <Fragment>
                                    <div className="col-auto vertical-gutter__item">
                                        {resultCount} <Trans t={resultCount === 1 ? 'listing.item': 'listing.items'}/>
                                    </div>

                                    {resultCount <= SHOW_ALL_COUNT_MAX ? (
                                        <div className="col-auto vertical-gutter__item">
                                            <DataPoolSelectAllComponent>
                                                {<Trans t="listing.select-all"/>}
                                            </DataPoolSelectAllComponent>
                                        </div>
                                    ) : null }
                                </Fragment>
                            ) : null }

                            <OrderByDropdownComponent
                                toggleIconComponent={<Bars height="18" width="16" className="mr-2"/>}
                                variant="link"
                                classNames={{toggle: 'd-flex align-items-center', wrapper: 'col-auto ml-auto ml-md-0 vertical-gutter__item dropdown--sm'}}
                                title={sortByName}/>

                            {forceTileView ? null : (
                                <SwitchViewButtonsComponent className={"col-auto vertical-gutter__item"}/>
                            )}
                        </div>
                    </div>
                </div>
            </div>

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
                        <EmptyResultComponent />
                    )
                    : (
                        (view === 'item-view/tile' || forceTileView) ? (
                            <TileViewComponent TeaserComponent={TeaserComponent} ids={itemIds}/>
                        ) : (
                            <ListViewComponent items={itemIds}/>
                        )
                    )
            )}

            {pageCount > 1 ? (
                <PaginationComponent/>
            ): null}

            <DownloadConfigModalComponent />
            <MultiDownloadMessageModalComponent />
            <DataPoolUpdateModalComponent />
            <EditMetaDataModalComponent />
            <CreatePublicShareModalComponent />

            <AddToCollectionModalComponent />
        </Fragment>
    )
}

export const mapStateToProps = state => {
    const currentPageNumber = getCurrentPageNumber(state);
    const currentPageFetchingState = getFetchingStateByPage(state, currentPageNumber);

    return {
        pageFetchingState: currentPageFetchingState,
        isFetching: currentPageFetchingState === FETCHING || currentPageFetchingState === NOT_ASKED,
        filterFetchingState: getFilterFetchingState(state),
        filterValuesFetchingState: getFilterStatesFetchingState(state),
        pageFetchingMessage: getFetchingMessageByPage(state, currentPageNumber),
        hasSelectedFilters: !!getSelectedFilterValues(state)
            .filter(filter => filter.value && filter.label).length,
        pageSize: getPageSize(state),
        pageCount: getPageCount(state),
        itemIds: getIdsByPageNumber(state, currentPageNumber),
        resultCount: getResultCount(state),
        view: getCurrentView(state),
        currentListParams: getListParams(state),
        listParamNames: getListParamNames(state),
        hasNavigation: getConfig('list.folders.active') || getConfig('list.tags.active')
    };
};

export default connect(mapStateToProps)(DataPoolList);