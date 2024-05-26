/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment, useState} from "react";
import {connect} from "react-redux";
import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import ActionBar from "~portal-engine/scripts/components/actions/ActionBar";
import ActionDropdown from "~portal-engine/scripts/components/actions/ActionDropdown";
import {useConfirmModal} from "~portal-engine/scripts/components/modals/ConfirmModal";
import {deleteCollection, renameCollection} from "~portal-engine/scripts/features/collections/collections-actions";
import {usePromptModal} from "~portal-engine/scripts/components/modals/PromptModal";
import {getItemById} from "~portal-engine/scripts/features/collections/collections-selectors";
import ShareCollectionModal from "~portal-engine/scripts/features/collections/components/ShareCollectionModal";
import {EDIT} from "~portal-engine/scripts/consts/permissions";
import {filterActionHandlerByPermissions} from "~portal-engine/scripts/components/actions";
import {ReactComponent as InfoCircle} from "~portal-engine/icons/info-circle";
import Tooltip from "react-bootstrap/Tooltip";
import OverlayTrigger from "react-bootstrap/OverlayTrigger";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import {publicShareCollectionListItemClicked} from "~portal-engine/scripts/features/public-share/public-share-actions";

function CollectionListItem({
    id,
    name,
    itemCount,
    creationDate,
    sharedByUserName,
    formattedSharedDate,
    isOwner = false,
    detailUrl,
    permission,
    actionHandler,
    actionUrls
}) {
    const [shareOpen, setShareOpen] = useState(false);

    actionHandler = filterActionHandlerByPermissions({
        permissions: {
            share: isOwner,
            publicShare: isOwner,
            delete: isOwner,
            edit: permission === EDIT

        },
        actionHandler: {
            ...actionHandler,
            onShare: () => setShareOpen(true),
        }
    });

    const sharedByText = useTranslation('collection.shared-by')
        .replace('%user%', sharedByUserName)
        .replace('%date%', formattedSharedDate);

    const renderSharedByTooltip = (props) => (
        <Tooltip id="button-tooltip" {...props}>
            {sharedByText}
        </Tooltip>
    );

    return (
        <tr className="data-table__row">
            <td>
                {detailUrl
                    ? (<a href={detailUrl}>
                        {name}
                    </a>)
                    : name
                }

                {sharedByUserName ? (
                    <OverlayTrigger
                        placement="top"
                        overlay={renderSharedByTooltip}>
                        <span className="text-muted ml-1 d-inline-block">
                            <InfoCircle height="20"/>
                        </span>
                    </OverlayTrigger>
                ) : null}
            </td>
            <td>{itemCount}</td>
            <td>{creationDate}</td>
            <td>
                <Media queries={{
                    small: MD_DOWN,
                }}>
                    {matches => (
                        matches.small
                            ? <ActionDropdown actionHandler={actionHandler} actionUrls={actionUrls}/>
                            : <ActionBar actionUrls={actionUrls} actionHandler={actionHandler}/>

                    )}
                </Media>

                <ShareCollectionModal collectionId={id} isOpen={shareOpen} onClose={() => setShareOpen(false)}/>
            </td>
        </tr>
    )
}

export const mapStateToProps = (state, {id}) => {
    let collection = getItemById(state, id);

    return ({
        ...collection,
        isOwner: collection.owner,
        actionUrls: {
            onDetail: collection.detailUrl
        }
    });
};

export const mapDispatchToProps = (dispatch, {id}) => {
    return {
        actionHandler: {
            onEdit: (name) => dispatch(renameCollection({id, name})),
            onDelete: () => dispatch(deleteCollection({id})),
            onPublicShare: () => dispatch(publicShareCollectionListItemClicked({
                collectionId: id
            }))
        }
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(function (props) {
    const {confirm, confirmModal} = useConfirmModal(props.actionHandler.onDelete, {
        title: <Trans t="collection.delete-confirm.title"/>,
        message: <Trans t="collection.delete-confirm.text"/>,
        cancelText: <Trans t="collection.delete-confirm.cancel"/>,
        confirmText: <Trans t="collection.delete-confirm.confirm"/>,
        confirmStyle: "danger",
    });

    const {prompt, promptModal} = usePromptModal(props.actionHandler.onEdit, {
        title: <Trans t="collection.rename-prompt.title"/>,
        label: <Trans t="collection.rename-prompt.label"/>,
        cancelText: <Trans t="collection.rename-prompt.cancel"/>,
        confirmText: <Trans t="collection.rename-prompt.confirm"/>,
    });

    let transformedProps = {
        ...props,
        actionHandler: {
            ...props.actionHandler,
            onEdit: () => prompt(props.name),
            onDelete: confirm
        }
    };

    return <Fragment>
        <CollectionListItem {...transformedProps}/>

        {confirmModal}
        {promptModal}
    </Fragment>
});