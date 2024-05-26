/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment, useState, useEffect} from "react";
import {connect} from "react-redux";
import ActionBar, {defaultActionConfig} from "~portal-engine/scripts/components/actions/ActionBar";
import {filterActionHandlerByPermissions} from "~portal-engine/scripts/components/actions";
import ShareCollectionModal from "~portal-engine/scripts/features/collections/components/ShareCollectionModal";
import {fetchCollectionDetailActions} from "~portal-engine/scripts/features/collections/collections-actions";
import {collectionMultiDownloadClicked} from "~portal-engine/scripts/features/download/download-actions";
import {
    getCollectionDetailActionsFetchingState,
    getCollectionDetailActions
} from "~portal-engine/scripts/features/collections/collections-selectors";
import {NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import CollectionDownloadMessageModal
    from "~portal-engine/scripts/features/download/components/DownloadConfig/CollectionDownloadMessageModal";
import {Dropdown} from "react-bootstrap";
import Trans from "~portal-engine/scripts/components/Trans";
import {ReactComponent as DownloadIcon} from "~portal-engine/icons/download";
import {noop} from "~portal-engine/scripts/utils/utils";
import {publicShareCollectionClicked} from "~portal-engine/scripts/features/public-share/public-share-actions";

export const mapStateToProps = (state) => ({
    collectionDetailActionsState: getCollectionDetailActionsFetchingState(state),
    collectionDetailActions: getCollectionDetailActions(state),
});

export const mapDispatchToProps = (dispatch, {id}) => ({
    fetchCollectionDetailActions: (collectionId) => dispatch(fetchCollectionDetailActions(collectionId)),
    onDownload: (dataPoolId) => dispatch(collectionMultiDownloadClicked(dataPoolId)),
    onPublicShare: ({dataPools}) => dispatch(publicShareCollectionClicked({
        collectionId: id,
        dataPools
    }))
});

export function CollectionDetailActions({
    id,
    collectionDetailActionsState,
    collectionDetailActions,
    fetchCollectionDetailActions,
    onDownload,
    onPublicShare = noop,
    actionHandler = {}
}) {
    const [shareOpen, setShareOpen] = useState(false);

    useEffect(() => {
        if (collectionDetailActionsState === NOT_ASKED) {
            fetchCollectionDetailActions({collectionId: id})
        }
    }, [collectionDetailActionsState]);


    if (collectionDetailActionsState === NOT_ASKED) {
        return null;
    }
    let activeDataPoolId = null;
    if (typeof collectionDetailActions.download !== 'undefined') {
        let downloadItem = collectionDetailActions.download[0];
        activeDataPoolId = downloadItem.dataPoolId;
    }

    actionHandler = filterActionHandlerByPermissions({
        permissions: {
            share: typeof collectionDetailActions.share !== 'undefined',
            download: typeof collectionDetailActions.download !== 'undefined',

        },
        actionHandler: {
            ...actionHandler,
            onShare: () => setShareOpen(true),
            onDownload: () => onDownload(activeDataPoolId),
            ...(collectionDetailActions.publicShare
                ? {
                    onPublicShare: () => onPublicShare({dataPools: collectionDetailActions.publicShare}),
                } : null)

        }
    });

    let transformedActions = (collectionDetailActions.download && collectionDetailActions.download.length > 1)
        ? defaultActionConfig.map(action => action.id === 'download'
            ? {
                ...action,
                Component: () => (
                    <Dropdown className={'action-bar__item'}>
                        <Dropdown.Toggle className={`btn icon-btn action-bar__button`}>
                        <span className="action-bar__item__title text-nowrap">
                            <Trans t="download" domain="action-bar"/>
                        </span>
                            <DownloadIcon className="icon-btn__icon"/>
                        </Dropdown.Toggle>
                        <Dropdown.Menu>
                            {collectionDetailActions.download.map(item => (
                                <Dropdown.Item key={item.dataPoolId}
                                               onClick={() => onDownload(item.dataPoolId)}>{item.name}</Dropdown.Item>
                            ))}
                        </Dropdown.Menu>
                    </Dropdown>
                )
            }
            : action
        )
        : defaultActionConfig;

    return (
        <Fragment>
            <ActionBar actions={transformedActions} actionHandler={actionHandler}/>

            <ShareCollectionModal collectionId={id} isOpen={shareOpen} onClose={() => setShareOpen(false)}/>

            <CollectionDownloadMessageModal/>
        </Fragment>
    )
}

export default connect(mapStateToProps, mapDispatchToProps)(CollectionDetailActions);