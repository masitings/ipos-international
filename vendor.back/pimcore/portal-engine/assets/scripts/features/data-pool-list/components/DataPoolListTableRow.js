/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from 'react';
import DataTableRow from "~portal-engine/scripts/components/DataTableRow";
import {connect} from "react-redux";
import {addToCartClicked, directDownloadClicked} from "~portal-engine/scripts/features/download/download-actions";
import {getConfig} from "~portal-engine/scripts/utils/general";
import {getItemById, isSelected} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import {toggleSelection, relocateItemClicked, deleteClicked} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {addToCollectionClicked} from "~portal-engine/scripts/features/collections/collections-actions";
import {filterActionHandlerByPermissions} from "~portal-engine/scripts/components/actions";
import {useConfirmModal} from "~portal-engine/scripts/components/modals/ConfirmModal";
import Trans from "~portal-engine/scripts/components/Trans";
import {publicShareClicked} from "~portal-engine/scripts/features/public-share/public-share-actions";

export function mapStateToProps(state, props) {
    let dataPoolItem = getItemById(state, props.id);
    let selected = isSelected(state, {
        id: props.id,
        dataPoolId: getConfig('currentDataPool.id'),
        collectionId: getConfig('collection.id'),
    });

    return {
        id: dataPoolItem.id,
        title: dataPoolItem.name,
        href : dataPoolItem.detailLink,
        detailListViewAttributes: dataPoolItem.listViewAttributes,
        isSelected: selected,
        permissions: dataPoolItem.permissions,
        image: {
            src: dataPoolItem.thumbnail,
            alt: dataPoolItem.name
        }
    }
}

export const mapDispatchToProps = (dispatch, {id, actionHandler}) => {
    return {
        onSelectedToggle: (isSelected) => dispatch(toggleSelection({
            id,
            isSelected,
            dataPoolId: getConfig("currentDataPool.id"),
            collectionId: getConfig("collection.id")
        })),
        actionHandler: {
            onDownload: (id) => dispatch(directDownloadClicked({
                ids: [id],
                dataPoolId: getConfig("currentDataPool.id")
            })),
            onAddToCart: (id) => dispatch(addToCartClicked({
                ids: [id],
                dataPoolId: getConfig("currentDataPool.id")
            })),
            onAddToCollection: (id) => dispatch(addToCollectionClicked({
                ids: [id],
                dataPoolId: getConfig("currentDataPool.id")
            })),
            onPublicShare: (id) => dispatch(publicShareClicked({
                ids: [id],
                dataPool: getConfig("currentDataPool")
            })),
            onUpdate: (id) => dispatch(relocateItemClicked({
                ids: [id],
                dataPoolId: getConfig("currentDataPool.id")
            })),
            onDelete: (id) => dispatch(deleteClicked({
                ids: [id],
                dataPoolId: getConfig("currentDataPool.id"),
                collectionId: getConfig("collection.id")
            })),
            ...actionHandler
        }
    };
};

export function DataPoolListTableRow(props) {
    let {permissions, actionHandler} = props;

    const {confirm, confirmModal} = useConfirmModal(props.actionHandler.onDelete, {
        title: <Trans t="asset.delete-confirm.title"/>,
        message: <Trans t="asset.delete-confirm.text"/>,
        cancelText: <Trans t="asset.delete-confirm.cancel"/>,
        confirmText: <Trans t="asset.delete-confirm.confirm"/>,
        confirmStyle: "danger",
    });

    actionHandler = filterActionHandlerByPermissions({
        permissions,
        actionHandler: {
            ...actionHandler,
            onDelete: confirm
        }
    });

    return (
        <Fragment>
            <DataTableRow {...props} actionHandler={actionHandler}/>

            {confirmModal}
        </Fragment>
    );
}

export default connect(mapStateToProps, mapDispatchToProps)(DataPoolListTableRow);