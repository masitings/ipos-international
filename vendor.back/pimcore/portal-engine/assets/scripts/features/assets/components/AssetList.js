/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from "react";
import {connect} from "react-redux";
import {getPermissions, getFetchingStateByPage, getCurrentPageNumber, getUploadFolder} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import DataPoolListNavigation from "~portal-engine/scripts/features/data-pool-list/components/DataPoolListNavigation";
import DataPoolSelectionBar from "~portal-engine/scripts/features/data-pool-list/components/DataPoolSelectionBar";
import DataPoolList, {
    List,
    mapStateToProps as datPoolListMapStateToProps
} from "~portal-engine/scripts/features/data-pool-list/components/DataPoolList";
import AssetFilters from "~portal-engine/scripts/features/assets/components/AssetFilters";
import AssetTileView from "~portal-engine/scripts/features/assets/components/AssetTileView";
import AssetListView from "~portal-engine/scripts/features/assets/components/AssetListView";
import AssetListEmpty from "~portal-engine/scripts/features/assets/components/AssetListEmpty";
import {FETCHING, NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import UploadModal from "~portal-engine/scripts/features/upload/components/upload-modal/UploadModal";
import SubfolderModal from "~portal-engine/scripts/features/subfolder/components/SubfolderModal";
import {getFilterBarButtons} from "~portal-engine/scripts/features/assets/assets-utils";
import Trans from "~portal-engine/scripts/components/Trans";

export function AssetList(props) {
    return (
        <Fragment>
            <DataPoolList
                MobileComponent={MobileList}
                FiltersComponent={AssetFilters}
                TileViewComponent={AssetTileView}
                ListViewComponent={AssetListView}
                EmptyResultComponent={AssetListEmpty}
                {...props}/>

            <UploadModal/>
            <SubfolderModal elementType="asset"/>
        </Fragment>
    )
}

export function MobileList(props) {
    const buttons = getFilterBarButtons({
        uploadFolder: props.uploadFolder,
        isFetching: props.isFetching,
        permissions: props.permissions,
        btnProps: {
            block: true
        }
    });

    return (
        <Fragment>
            <div className="container full-height-layout__fit mb-3">
                <div className="row row-gutter--2 vertical-gutter vertical-gutter--2 button-row">
                    <div className="col vertical-gutter__item">
                        <AssetFilters subTitle={props.hasNavigation
                            ? <Trans t="filter.overlay-filter-title"/>
                            : null
                        }>
                            {props.hasNavigation ? (
                                <section className="my-4">
                                    <h5><Trans t="filter.overlay-navigation-title"/></h5>

                                    <DataPoolListNavigation/>
                                </section>
                            ) : null}
                        </AssetFilters>
                    </div>

                    {buttons.map((button, i) => (
                        <div className="col text-nowrap vertical-gutter__item" key={i}>
                            {button}
                        </div>
                    ))}
                </div>
            </div>

            <div className="container full-height-layout__fill main-content__main">
                <List {...props} forceTileView={true}/>
            </div>

            <DataPoolSelectionBar className="full-height-layout__fit"/>
        </Fragment>
    );
}

export function mapStateToProps(state, ownProps) {
    const currentPageNumber = getCurrentPageNumber(state);
    const currentPageFetchingState = getFetchingStateByPage(state, currentPageNumber);

    return {
        ...datPoolListMapStateToProps(state, ownProps),
        uploadFolder: getUploadFolder(state),
        permissions: getPermissions(state),
        isFetching: currentPageFetchingState === FETCHING || currentPageFetchingState === NOT_ASKED,
    }
}

export default connect(mapStateToProps)(AssetList);