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
import {ReactComponent as DownloadIcon} from "~portal-engine/icons/download";
import {ReactComponent as ShoppingBagIcon} from "~portal-engine/icons/shopping-bag";
import {ReactComponent as EditIcon} from "~portal-engine/icons/edit";
import {ReactComponent as TrashAltIcon} from "~portal-engine/icons/trash-alt";
import {ReactComponent as ShareIcon} from "~portal-engine/icons/share-alt";
import {ReactComponent as LinkIcon} from "~portal-engine/icons/external-link-alt";
import {ReactComponent as UsersIcon} from "~portal-engine/icons/users";
import {ReactComponent as MoveIcon} from "~portal-engine/icons/exchange-alt";
import {ReactComponent as DocumentsIcon} from "~portal-engine/icons/documents";
import {ReactComponent as MinusCircleIcon} from "~portal-engine/icons/minus-circle";
import Trans from "~portal-engine/scripts/components/Trans";

export const defaultActionConfig = [
    {
        id: 'collections-remove',
        translationKey: 'remove-from-collection',
        handlerName: 'onRemoveFromCollection',
        Icon: MinusCircleIcon,
    },
    {
        id: 'collections',
        translationKey: 'add-to-collection',
        handlerName: 'onAddToCollection',
        Icon: DocumentsIcon,
    },
    {
        id: 'cart',
        translationKey: 'add-cart',
        handlerName: 'onAddToCart',
        Icon: ShoppingBagIcon,
    },
    {
        id: 'download',
        translationKey: 'download',
        handlerName: 'onDownload',
        Icon: DownloadIcon,
    },
    {
        id: 'edit',
        translationKey: 'edit',
        handlerName: 'onEdit',
        Icon: EditIcon,
    },
    {
        id: 'update',
        translationKey: 'relocate',
        handlerName: 'onUpdate', /*todo rename*/
        Icon: MoveIcon,
    },
    {
        id: 'share',
        translationKey: 'share',
        handlerName: 'onShare',
        Icon: UsersIcon,
    },
    {
        id: 'publicShare',
        translationKey: 'public-share',
        handlerName: 'onPublicShare',
        Icon: ShareIcon,
    },
    {
        id: 'delete',
        translationKey: 'delete',
        handlerName: 'onDelete',
        Icon: TrashAltIcon,
    },
    {
        id: 'detail',
        translationKey: 'open',
        handlerName: 'onDetail',
        Icon: LinkIcon,
    }
];

export default function (props) {
    const {
        className = "",
        isLarge = false,
        actions = defaultActionConfig,
        actionHandler = {},
        actionUrls = {},
        actionUrlsTarget = '_self',
        allowWrap = true
    } = props;

    return (
        <div className={`action-bar ${allowWrap ? '': 'action-bar--no-wrap'} ${className}`} role="group" aria-label="Actions">
            {actions
                .filter(({handlerName}) =>
                    (actionHandler[handlerName] && typeof actionHandler[handlerName] === "function")
                    || (actionUrls[handlerName] && typeof actionUrls[handlerName] === "string"
                    ))
                .map(({id, Icon, handlerName, translationKey, Component}) => {
                    return (
                        Component
                            ? <Component key={id}/>
                            : actionUrls[handlerName]
                            ? (
                                <a href={actionUrls[handlerName]}
                                   target={actionUrlsTarget}
                                   key={id}
                                   className={`btn icon-btn action-bar__item action-bar__button ${isLarge ? 'icon-btn--lg' : ''}`}>
                                        <span className="action-bar__item__title text-nowrap">
                                            <Trans t={translationKey} domain="action-bar"/>
                                        </span>
                                    <Icon className="icon-btn__icon"/>
                                </a>
                            ) : (
                                <Fragment key={id}>
                                    <button type="button"
                                            className={`btn icon-btn action-bar__item action-bar__button ${isLarge ? 'icon-btn--lg' : ''}`}
                                            onClick={actionHandler[handlerName]}>
                                            <span className="action-bar__item__title text-nowrap">
                                                <Trans t={translationKey} domain="action-bar"/>
                                            </span>
                                        <Icon className="icon-btn__icon"/>
                                    </button>

                                    {actionHandler.Component ? <actionHandler.Component/> : null}
                                </Fragment>
                            )
                    )
                })
            }

        </div>
    );
}