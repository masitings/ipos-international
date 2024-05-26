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
import ActionBar from "~portal-engine/scripts/components/actions/ActionBar";
import Breadcrumb from "~portal-engine/scripts/components/Breadcrumb";
import Trans from "~portal-engine/scripts/components/Trans";
import Tabs from "~portal-engine/scripts/components/tab/Tabs";
import Tab from "~portal-engine/scripts/components/tab/Tab";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import SidebarLayout from "~portal-engine/scripts/components/layouts/SidebarLayout";
import {
    getDetailData,
    getDetailError,
    getDetailFetchingState,
    getDirectEditStatus
} from "~portal-engine/scripts/features/asset/asset-selectors";
import {fetchDetail, startDirectEdit} from "~portal-engine/scripts/features/asset/asset-actions";
import {FAILED, NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import Overview from "~portal-engine/scripts/features/asset/components/detail/Overview";
import MetaData from "~portal-engine/scripts/features/asset/components/detail/MetaData";
import EmbeddedMetaData from "~portal-engine/scripts/features/asset/components/detail/EmbeddedMetaData";
import Versions from "~portal-engine/scripts/features/asset/components/detail/Versions";
import {getConfig, showError} from "~portal-engine/scripts/utils/general";
import {addToCartClicked, directDownloadClicked} from "~portal-engine/scripts/features/download/download-actions";
import {addToCollectionClicked} from "~portal-engine/scripts/features/collections/collections-actions";
import {filterActionHandlerByPermissions} from "~portal-engine/scripts/components/actions";
import * as UPLOAD_MODES from "~portal-engine/scripts/consts/upload-modes";
import {modalOpened} from "~portal-engine/scripts/features/upload/upload-actions";
import UploadModal from "~portal-engine/scripts/features/upload/components/upload-modal/UploadModal";
import AddToCollectionModal from "~portal-engine/scripts/features/collections/components/AddToCollectionModal";
import MultiDownloadMessageModal
    from "~portal-engine/scripts/features/download/components/DownloadConfig/MultiDownloadMessageModal";
import DownloadConfigModal
    from "~portal-engine/scripts/features/download/components/DownloadConfig/DownloadConfigModal";
import DataPoolSidebarList from "~portal-engine/scripts/features/data-pool-list/components/DataPoolSidebarList";
import ImagePreview from "~portal-engine/scripts/features/asset/components/detail/preview/ImagePreview";
import VideoPreview from "~portal-engine/scripts/features/asset/components/detail/preview/VideoPreview";
import DocumentPreview from "~portal-engine/scripts/features/asset/components/detail/preview/DocumentPreview";
import {defaultActionConfig} from "../../../components/actions/ActionBar";
import {ReactComponent as UploadIcon} from "~portal-engine/icons/arrow-alt-circle-up";
import {ReactComponent as EditIcon} from "~portal-engine/icons/edit";
import {deleteClicked, relocateItemClicked} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {useConfirmModal} from "~portal-engine/scripts/components/modals/ConfirmModal";
import DataPoolUpdateModal from "~portal-engine/scripts/features/data-pool-list/components/DataPoolUpdateModal";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import DirectEditStatus from "~portal-engine/scripts/features/asset/components/detail/DirectEditStatus";
import {isPublicShare} from "~portal-engine/scripts/features/public-share/public-share-selectors";
import {publicShareClicked} from "~portal-engine/scripts/features/public-share/public-share-actions";
import CreatePublicShareModal from "~portal-engine/scripts/features/public-share/components/CreatePublicShareModal";

export const mapStateToProps = (state) => ({
    detailFetchingState: getDetailFetchingState(state),
    detailError: getDetailError(state),
    detail: getDetailData(state),
    directEditStatus: getDirectEditStatus(state),
    isPublicShare: isPublicShare(state)
});

export const mapDispatchToProps = (dispatch) => ({
    fetchDetail: () => dispatch(fetchDetail()),
    createActionHandler: (id) => ({
        onDownload: () => dispatch(directDownloadClicked({
            ids: [id],
            dataPoolId: getConfig("currentDataPool.id")
        })),
        onReplace: () => dispatch(modalOpened({
            mode: UPLOAD_MODES.SINGLE_REPLACE,
            context: {
                id: id
            }
        })),
        onDelete: () => dispatch(deleteClicked({
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
        onUpdate: () => dispatch(relocateItemClicked({
            ids: [id],
            dataPoolId: getConfig("currentDataPool.id")
        })),
        onDirectEdit: () => dispatch(startDirectEdit()),
        onPublicShare: () => {
            dispatch(publicShareClicked({
                ids: [id],
                dataPool: getConfig("currentDataPool")
            }))
        }
    })
});

export function getPreviewComponent(type) {
    switch (type) {
        case "image":
            return ImagePreview;

        case "video":
            return VideoPreview;

        case "document":
            return DocumentPreview;

        default:
            return null;
    }
}

export function AssetDetail({detailFetchingState, detailError, directEditStatus, fetchDetail, detail, createActionHandler, versionsEnabled, isPublicShare = false}) {
    let actionHandler = createActionHandler(detail ? detail.id : null);
    const doDelete = actionHandler.onDelete;

    const {confirm: confirmDelete, confirmModal: confirmDeleteModal} = useConfirmModal(() => {
        doDelete().then(() => {
            if(Array.isArray(detail.breadcrumbs) && detail.breadcrumbs) {
                window.location = detail.breadcrumbs[0].url;
            }
        })
    }, {
        title: <Trans t="asset.delete-confirm.title"/>,
        message: <Trans t="asset.delete-confirm.text"/>,
        cancelText: <Trans t="asset.delete-confirm.cancel"/>,
        confirmText: <Trans t="asset.delete-confirm.confirm"/>,
        confirmStyle: "danger",
    });

    actionHandler = {
        ...actionHandler,
        onDelete: confirmDelete
    };

    useEffect(() => {
        if (detailFetchingState === NOT_ASKED) {
            fetchDetail();
        }
    }, [detailFetchingState]);

    if (detailFetchingState !== SUCCESS) {
        if (detailFetchingState === FAILED) {
            showError(detailError);
        }

        return (
            <LoadingIndicator className="my-4"/>
        );
    }

    const PreviewComponent = getPreviewComponent(detail.type);
    const actions = [
        ...defaultActionConfig,
        {
            id: "replace",
            translationKey: "replace",
            handlerName: "onReplace",
            Icon: UploadIcon
        }
    ];

    if(detail.permissions.update && detail.directEditSupported) {
        actions.push({
            id: "direct_edit",
            translationKey: "direct-edit",
            handlerName: "onDirectEdit",
            Icon: EditIcon
        });
    }

    if (isPublicShare) {
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
                        ? <div className="container main-content__main">
                            <div className="row row-gutter--3 align-items-center justify-content-between mb-4 vertical-gutter--3">
                                <div className="col-auto vertical-gutter__item">
                                    <Breadcrumb breadcrumbs={detail.breadcrumbs}/>
                                </div>

                                <div className="col-auto vertical-gutter__item">
                                    <ActionBar
                                        actions={actions.filter(action => action.id !== 'direct_edit')}
                                        actionHandler={filterActionHandlerByPermissions({
                                            permissions: detail.permissions,
                                            actionHandler
                                        })}
                                    />
                                </div>
                            </div>

                            <Tabs classNames={{wrapper: "nav-level--1 nav-tabs--bar-sm"}}>
                                <Tab tab="overview" label={(<Trans t="overview" domain="asset"/>)}>
                                    <Overview PreviewComponent={PreviewComponent}/>
                                </Tab>

                                <Tab tab="meta-data" label={(<Trans t="meta-data" domain="asset"/>)}>
                                    <MetaData PreviewComponent={PreviewComponent}/>
                                </Tab>

                                {!!detail.embeddedMetadata &&
                                <Tab tab="embedded-meta-data" label={(<Trans t="embedded-meta-data" domain="asset"/>)}>
                                    <EmbeddedMetaData data={detail.embeddedMetadata}/>
                                </Tab>
                                }

                                {versionsEnabled &&
                                <Tab tab="versions" label={(<Trans t="versions" domain="asset"/>)}>
                                    <Versions/>
                                </Tab>
                                }
                            </Tabs>
                        </div>
                        : <SidebarLayout sidebarChildren={(<DataPoolSidebarList/>)}>
                            <Fragment>
                                <div className="row row-gutter--3 align-items-center justify-content-between mb-5 vertical-gutter--3">
                                    <div className="col-auto vertical-gutter__item">
                                        <Breadcrumb breadcrumbs={detail.breadcrumbs}/>
                                    </div>

                                    <div className="col-auto vertical-gutter__item">
                                        <ActionBar
                                            actions={actions}
                                            actionHandler={filterActionHandlerByPermissions({permissions: detail.permissions, actionHandler})}
                                        />
                                    </div>
                                </div>

                                <DirectEditStatus/>

                                <Tabs classNames={{wrapper: "nav-level--1 nav-tabs--bar-sm"}}>
                                    <Tab tab="overview" label={(<Trans t="overview" domain="asset"/>)}>
                                        <Overview PreviewComponent={PreviewComponent}/>
                                    </Tab>

                                    <Tab tab="meta-data" label={(<Trans t="meta-data" domain="asset"/>)}>
                                        <MetaData PreviewComponent={PreviewComponent}/>
                                    </Tab>

                                    {!!detail.embeddedMetadata &&
                                    <Tab tab="embedded-meta-data" label={(<Trans t="embedded-meta-data" domain="asset"/>)}>
                                        <EmbeddedMetaData data={detail.embeddedMetadata}/>
                                    </Tab>
                                    }

                                    {versionsEnabled &&
                                    <Tab tab="versions" label={(<Trans t="versions" domain="asset"/>)}>
                                        <Versions/>
                                    </Tab>
                                    }
                                </Tabs>
                            </Fragment>
                        </SidebarLayout>
                )}
            </Media>

            <AddToCollectionModal/>
            <DownloadConfigModal/>
            <MultiDownloadMessageModal/>
            <DataPoolUpdateModal fromDetailPage={true} parentPath={getConfig('list.folders.root.path')} currentPath={getConfig('list.folders.root.path') + detail.path.replace(/\/$/, "")} submitCallback={() => {
                fetchDetail();
            }}/>
            {confirmDeleteModal}
            <UploadModal/>
            <CreatePublicShareModal/>
        </Fragment>
    )
}

export default connect(mapStateToProps, mapDispatchToProps)(AssetDetail);