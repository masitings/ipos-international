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
import Breadcrumb from "~portal-engine/scripts/components/Breadcrumb";
import DataPoolSidebarList from "~portal-engine/scripts/features/data-pool-list/components/DataPoolSidebarList";
import Data from "~portal-engine/scripts/features/data-objects/components/detail/Data";
import SidebarLayout from "~portal-engine/scripts/components/layouts/SidebarLayout";
import {fetchDetail} from "~portal-engine/scripts/features/data-objects/data-object-actions";
import {addToCartClicked, directDownloadClicked} from "~portal-engine/scripts/features/download/download-actions";
import {addToCollectionClicked} from "~portal-engine/scripts/features/collections/collections-actions";
import {
    getDetailBreadcrumbs,
    getDetailError,
    getDetailFetchingState,
    getDataObjectId,
    getPermissions
} from "~portal-engine/scripts/features/data-objects/data-object-selectors";
import {getConfig} from "~portal-engine/scripts/utils/general";
import {FAILED, NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import {showError} from "~portal-engine/scripts/utils/general";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import DownloadConfigModal
    from "~portal-engine/scripts/features/download/components/DownloadConfig/DownloadConfigModal";
import ActionBar from "~portal-engine/scripts/components/actions/ActionBar"
import AddToCollectionModal from "~portal-engine/scripts/features/collections/components/AddToCollectionModal";
import MultiDownloadMessageModal
    from "~portal-engine/scripts/features/download/components/DownloadConfig/MultiDownloadMessageModal";
import {filterActionHandlerByPermissions} from "~portal-engine/scripts/components/actions";
import {isPublicShare} from "~portal-engine/scripts/features/public-share/public-share-selectors";
import CreatePublicShareModal from "~portal-engine/scripts/features/public-share/components/CreatePublicShareModal";
import {publicShareClicked} from "~portal-engine/scripts/features/public-share/public-share-actions";

export const mapStateToProps = state => ({
    fetchingState: getDetailFetchingState(state),
    dataObjectId: getDataObjectId(state),
    error: getDetailError(state),
    breadcrumbs: getDetailBreadcrumbs(state),
    permissions: getPermissions(state),
    isPublicShare: isPublicShare(state)
});

export const mapDispatchToProps = (dispatch) => ({
    fetchDetail: () => dispatch(fetchDetail()),
    createActionHandler: (id) => ({
        onDownload: () => dispatch(directDownloadClicked({
            ids: [id],
            dataPoolId: getConfig("currentDataPool.id")
        })),
        onAddToCart: () => dispatch(addToCartClicked({
            ids: [id],
            dataPoolId: getConfig("currentDataPool.id")
        })),
        onAddToCollection: () => dispatch(addToCollectionClicked({
            ids: [id],
            dataPoolId: getConfig("currentDataPool.id")
        })),
        onPublicShare: () => {
            dispatch(publicShareClicked({
                ids: [id],
                dataPool: getConfig("currentDataPool")
            }))
        }
    })
});

export function DataObjectDetail(props) {
    const {
        fetchingState,
        error,
        fetchDetail
    } = props;

    useEffect(() => {
        if (fetchingState === NOT_ASKED) {
            fetchDetail();
        }
    }, [fetchingState]);

    if (fetchingState !== SUCCESS) {
        if (fetchingState === FAILED) {
            showError(error);
        }

        return (
            <LoadingIndicator className="my-4"/>
        );
    }

    let actionHandler = props.createActionHandler(props.dataObjectId);
    if (props.isPublicShare) {
        actionHandler = {
            onDownload: actionHandler.onDownload
        }
    }



    return (
        <Fragment>
            <Media queries={{
                small: MD_DOWN,
            }}>
                {matches => (
                    matches.small
                        ? <MobileDataObjectDetailComponent {...props} actionHandler={actionHandler}/>
                        : <DesktopDataObjectDetailComponent {...props} actionHandler={actionHandler}/>
                )}
            </Media>

            <AddToCollectionModal/>
            <DownloadConfigModal/>
            <MultiDownloadMessageModal/>
            <CreatePublicShareModal/>
        </Fragment>

    );
}

export function MobileDataObjectDetailComponent(props) {
    return (
        <div className="container main-content__main">
            <div className="row row-gutter--3 align-items-center justify-content-between mb-5 vertical-gutter--3">
                <div className="col-auto vertical-gutter__item">
                    <Breadcrumb breadcrumbs={props.breadcrumbs}/>
                </div>

                <div className="col-auto vertical-gutter__item">
                    <ActionBar actionHandler={filterActionHandlerByPermissions({
                        permissions: props.permissions,
                        actionHandler: props.actionHandler
                    })}/>
                </div>
            </div>

            <Data/>
        </div>
    );
}

export function DesktopDataObjectDetailComponent(props) {
    return (
        <SidebarLayout sidebarChildren={(<DataPoolSidebarList />)}>
            <Fragment>
                <div className="d-flex align-items-center justify-content-between mb-5">
                    <Breadcrumb breadcrumbs={props.breadcrumbs}/>

                    <ActionBar actionHandler={filterActionHandlerByPermissions({permissions: props.permissions, actionHandler: props.actionHandler})}/>
                </div>

                <Data/>
            </Fragment>
        </SidebarLayout>
    )
}

export default connect(mapStateToProps, mapDispatchToProps)(DataObjectDetail);