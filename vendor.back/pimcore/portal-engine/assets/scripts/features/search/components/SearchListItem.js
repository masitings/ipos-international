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
import {useConfirmModal} from "~portal-engine/scripts/components/modals/ConfirmModal";
import {deleteSearch, renameSearch} from "~portal-engine/scripts/features/search/search-actions";
import {usePromptModal} from "~portal-engine/scripts/components/modals/PromptModal";
import {getItemById} from "~portal-engine/scripts/features/search/search-selectors";
import ShareSearchModal from "~portal-engine/scripts/features/search/components/ShareSearchModal";
import {filterActionHandlerByPermissions} from "~portal-engine/scripts/components/actions";
import {ReactComponent as InfoCircle} from "~portal-engine/icons/info-circle";
import Tooltip from "react-bootstrap/Tooltip";
import OverlayTrigger from "react-bootstrap/OverlayTrigger";
import {defaultActionConfig} from "~portal-engine/scripts/components/actions/ActionBar";
import {ReactComponent as TrashAltIcon} from "~portal-engine/icons/trash-alt";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import ActionDropdown from "~portal-engine/scripts/components/actions/ActionDropdown";

export function SearchListItem({
    id,
    name,
    creationDate,
    sharedByUserName,
    formattedSharedDate,
    isOwner = false,
    sharedWith,
    detailUrl,
    actionHandler,
    actionUrls
}) {
    const [shareOpen, setShareOpen] = useState(false);

    actionHandler = filterActionHandlerByPermissions({
        permissions: {
            share: isOwner,
            delete: isOwner || sharedWith === "user",
            edit: isOwner
        },
        actionHandler: {
            ...actionHandler,
            onShare: () => setShareOpen(true)
        }
    });

    let actions = defaultActionConfig;

    if(sharedWith === "user") {
        // filter out delete and replace it with another translation key
        actions = actions.filter(action => action.id !== "delete");

        actions.push({
            id: 'delete',
            translationKey: 'remove-from-my-list',
            handlerName: 'onDelete',
            Icon: TrashAltIcon,
        })
    }

    const sharedByText = useTranslation('search.shared-by')
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
                    // <Trans t=""/>
                    <OverlayTrigger
                        placement="top"
                        overlay={renderSharedByTooltip}>
                        <span className="text-muted ml-1 d-inline-block">
                            <InfoCircle height="20"/>
                        </span>
                    </OverlayTrigger>
                ) : null}
            </td>
            <td>{creationDate}</td>
            <td>
                <Media queries={{
                    small: MD_DOWN,
                }}>
                    {matches => (
                        matches.small
                            ? <ActionDropdown actions={actions} actionUrls={actionUrls} actionHandler={actionHandler}/>
                            : <ActionBar actions={actions} actionUrls={actionUrls} actionHandler={actionHandler}/>

                    )}
                </Media>

                <ShareSearchModal searchId={id} isOpen={shareOpen} onClose={() => setShareOpen(false)}/>
            </td>
        </tr>
    )
}

export const mapStateToProps = (state, {id}) => {
    let search = getItemById(state, id);

    return ({
        ...search,
        isOwner: search.owner,
        actionUrls: {
            onDetail: search.detailUrl
        }
    });
};

export const mapDispatchToProps = (dispatch, {id}) => {
    return {
        actionHandler: {
            onEdit: (name) => dispatch(renameSearch({id, name})),
            onDelete: () => dispatch(deleteSearch({id}))
        }
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(function (props) {
    const {confirm, confirmModal} = useConfirmModal(props.actionHandler.onDelete, {
        title: <Trans t="search.delete-confirm.title"/>,
        message: <Trans t="search.delete-confirm.text"/>,
        cancelText: <Trans t="search.delete-confirm.cancel"/>,
        confirmText: <Trans t="search.delete-confirm.confirm"/>,
        confirmStyle: "danger",
    });

    const {prompt, promptModal} = usePromptModal(props.actionHandler.onEdit, {
        title: <Trans t="search.rename-prompt.title"/>,
        label: <Trans t="search.rename-prompt.label"/>,
        cancelText: <Trans t="search.rename-prompt.cancel"/>,
        confirmText: <Trans t="search.rename-prompt.confirm"/>,
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
        <SearchListItem {...transformedProps}/>

        {confirmModal}
        {promptModal}
    </Fragment>
});