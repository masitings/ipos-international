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
import Trans from "~portal-engine/scripts/components/Trans";
import {useConfirmModal} from "~portal-engine/scripts/components/modals/ConfirmModal";
import {getItemById} from "~portal-engine/scripts/features/public-share/public-share-selectors";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import ActionDropdown from "~portal-engine/scripts/components/actions/ActionDropdown";
import ActionBar from "~portal-engine/scripts/components/actions/ActionBar";
import Checkbox from "~portal-engine/scripts/components/Checkbox";
import {
    deletePublicShare,
    publicShareEditClicked
} from "~portal-engine/scripts/features/public-share/public-share-actions";
import {getLanguage} from "~portal-engine/scripts/utils/intl";
import EditPublicShareModal from "~portal-engine/scripts/features/public-share/components/EditPublicShareModal";

export function PublicShareListItem({
    id,
    name,
    itemCount,
    expiryDate,
    showTermsText,
    termsText,
    detailUrl,
    actionHandler,
    actionUrls
}) {
    const [isEditOpen, setEditOpen] = useState(false);

    actionHandler = {
        ...actionHandler,
        onEdit: () => {
            return setEditOpen(true);
        }
    };

    const isExpired = new Date(expiryDate * 1000).getTime() < new Date().getTime();

    if (isExpired) {
        actionUrls.onDetail = null;
    }

    return (
        <tr className="data-table__row">
            <td>
                {(detailUrl && !isExpired)
                    ? (<a target="_blank" href={detailUrl}>
                        {name}
                    </a>)
                    : name
                }
            </td>
            <td>{itemCount}</td>
            <td>{new Date(expiryDate * 1000).toLocaleDateString(getLanguage())}</td>
            <td className="text-center">
                <Checkbox readOnly={true} checked={showTermsText}/>
            </td>
            <td>{termsText}</td>
            <td className="text-nowrap">
                <Media queries={{
                    small: MD_DOWN,
                }}>
                    {matches => (
                        matches.small
                            ? <ActionDropdown actionHandler={actionHandler} actionUrls={actionUrls} actionUrlsTarget="_blank"/>
                            : <ActionBar allowWrap={false} actionUrls={actionUrls} actionHandler={actionHandler} actionUrlsTarget="_blank"/>
                    )}
                </Media>

                <EditPublicShareModal id={id} isOpen={isEditOpen} onClose={() => setEditOpen(false)}/>
            </td>
        </tr>
    )
}

export const mapStateToProps = (state, {id}) => {
    let publicShare = getItemById(state, id);

    return ({
        ...publicShare,
        actionUrls: {
            onDetail: publicShare.detailUrl
        }
    });
};

export const mapDispatchToProps = (dispatch, {id}) => {
    return {
        actionHandler: {
            onEdit: () => dispatch(publicShareEditClicked({id})),
            onDelete: () => dispatch(deletePublicShare({id}))
        }
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(function (props) {
    const {confirm, confirmModal} = useConfirmModal(props.actionHandler.onDelete, {
        title: <Trans t="public-share.delete-confirm.title"/>,
        message: <Trans t="public-share.delete-confirm.text"/>,
        cancelText: <Trans t="public-share.delete-confirm.cancel"/>,
        confirmText: <Trans t="public-share.delete-confirm.confirm"/>,
        confirmStyle: "danger",
    });

    let transformedProps = {
        ...props,
        actionHandler: {
            ...props.actionHandler,
            onDelete: confirm
        }
    };

    return <Fragment>
        <PublicShareListItem {...transformedProps}/>

        {confirmModal}
    </Fragment>
});