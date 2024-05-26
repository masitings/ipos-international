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
import SelectionBar from "~portal-engine/scripts/components/SelectionBar";
import {connect} from "react-redux";
import {addToCartClicked, directDownloadClicked} from "~portal-engine/scripts/features/download/download-actions";
import {getConfig} from "~portal-engine/scripts/utils/general";
import {
    getAllSelectedIds,
    getSelectedItemsFetchState,
    getItemById
} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import {
    toggleSelectionAll,
    requestSelectedItems,
    relocateItemClicked,
    deleteClicked
} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {editMetaDataClicked} from "~portal-engine/scripts/features/asset/asset-actions";
import {addToCollectionClicked} from "~portal-engine/scripts/features/collections/collections-actions";
import {filterActionHandlerByPermissions, mergePermissionList} from "~portal-engine/scripts/components/actions";
import {truthy} from "~portal-engine/scripts/utils/utils";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import {SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import {useConfirmModal} from "~portal-engine/scripts/components/modals/ConfirmModal";
import Trans from "~portal-engine/scripts/components/Trans";
import {getObjectFromLocalStorage, setObjectToLocalStorage} from "~portal-engine/scripts/utils/utils";
import {getSelectionKey} from "~portal-engine/scripts/sliceHelper/filter-list/filter-list-utils";
import {LIST_SELECTION_PERMISSION} from "~portal-engine/scripts/consts/local-storage-keys";
import {publicShareClicked} from "~portal-engine/scripts/features/public-share/public-share-actions";
import DataPoolSelectionBarTeaser
    from "~portal-engine/scripts/features/data-pool-list/components/DataPoolSelectionBarTeaser";

export const SelectionBarList = ({ids, isLoading}) => {
    return (
        isLoading ? (
            <LoadingIndicator className="my-4"/>
        ) : (
            <ul className="list-unstyled scroll-area row flex-nowrap mb-0 pb-2 teaser-grid teaser-grid--sm">
                {ids.map((id, index) => (
                    <li key={index} className="col-5 col-md teaser-grid__item">
                        <DataPoolSelectionBarTeaser id={id}/>
                    </li>
                ))}
            </ul>
        )
    )
};

export function mapStateToProps(state) {
    let dataPoolId = getConfig('currentDataPool.id'),
        collectionId = getConfig('collection.id'),
        publicShareHash = getConfig('publicShare.hash');

    let selectedIds = getAllSelectedIds(state, {
        dataPoolId: dataPoolId,
        collectionId: collectionId,
        publicShareHash: publicShareHash
    });

    let isLoading = true;
    if (getSelectedItemsFetchState(state) === SUCCESS) {
        isLoading = false;
    }

    let permissions = mergePermissionList(selectedIds.map(id => getItemById(state, id))
        .filter(truthy)
        .map(({permissions}) => permissions)
        .filter(truthy));


    let items = selectedIds.map(id => getItemById(state, id))
        .filter(truthy)
        .map(({id}) => id)
        .filter(truthy);

    if (items.length !== selectedIds.length) {
        let selectionKey = getSelectionKey({dataPoolId, collectionId});
        let selectedIdsPermissionsByDataPoolId = getObjectFromLocalStorage(LIST_SELECTION_PERMISSION) || {};


        permissions = selectedIdsPermissionsByDataPoolId[selectionKey]
    }

    return {
        selectedIds: selectedIds,
        isLoading: isLoading,
        permissions,
        dataPoolId,
        collectionId,
        ListComponent: SelectionBarList,
    }
}

export const mapDispatchToProps = (dispatch, {actionHandler}) => {
    return {
        onShowSelected: (selectedIds) => dispatch(requestSelectedItems(selectedIds)),
        onDeSelectAll: (selectedIds) => dispatch(toggleSelectionAll({
            ids: selectedIds,
            isSelected: false,
            dataPoolId: getConfig("currentDataPool.id"),
            collectionId: getConfig("collection.id"),
        })),
        actionHandler: {
            onDownload: (ids) => dispatch(directDownloadClicked({
                ids: ids,
                dataPoolId: getConfig("currentDataPool.id")
            })),
            onAddToCart: (ids) => dispatch(addToCartClicked({
                ids: ids,
                dataPoolId: getConfig("currentDataPool.id")
            })),
            onAddToCollection: (ids) => dispatch(addToCollectionClicked({
                ids: ids,
                dataPoolId: getConfig("currentDataPool.id")
            })),
            onPublicShare: (ids) => dispatch(publicShareClicked({
                ids: ids,
                dataPool: getConfig("currentDataPool")
            })),
            onUpdate: (ids) => dispatch(relocateItemClicked({
                ids: ids,
                dataPoolId: getConfig("currentDataPool.id")
            })),
            onDelete: (ids) => dispatch(deleteClicked({
                ids: ids,
                dataPoolId: getConfig("currentDataPool.id"),
                collectionId: getConfig("collection.id"),
            })),
            onEdit: (ids) => dispatch(editMetaDataClicked({
                ids: ids,
                dataPoolId: getConfig("currentDataPool.id"),
                collectionId: getConfig("collection.id"),
            })),
            ...actionHandler
        }
    };
};

export function DataPoolSelectionBar(props) {
    let {permissions, actionHandler, dataPoolId, collectionId} = props;

    const {confirm, confirmModal} = useConfirmModal(props.actionHandler.onDelete, {
        title: <Trans t="asset.delete-confirm.title"/>,
        message: <Trans t="asset.delete-confirm.text"/>,
        cancelText: <Trans t="asset.delete-confirm.cancel"/>,
        confirmText: <Trans t="asset.delete-confirm.confirm"/>,
        confirmStyle: "danger",
    });

    let selectionKey = getSelectionKey({dataPoolId, collectionId});
    let selectedIdsPermissionsByDataPoolId = getObjectFromLocalStorage(LIST_SELECTION_PERMISSION) || {};

    selectedIdsPermissionsByDataPoolId[selectionKey] = permissions;
    setObjectToLocalStorage(LIST_SELECTION_PERMISSION, selectedIdsPermissionsByDataPoolId);

    actionHandler = filterActionHandlerByPermissions({
        permissions,
        actionHandler: {
            ...actionHandler,
            onDelete: confirm
        }
    });


    return (<Fragment>
            <SelectionBar {...props} actionHandler={actionHandler}/>

            {confirmModal}
        </Fragment>
    );
}

export default connect(mapStateToProps, mapDispatchToProps)(DataPoolSelectionBar);