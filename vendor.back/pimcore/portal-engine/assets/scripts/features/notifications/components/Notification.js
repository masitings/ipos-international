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
import {noop} from "~portal-engine/scripts/utils/utils";
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import {connect} from "react-redux";
import {getNotificationById} from "~portal-engine/scripts/features/notifications/notifications-selectors";
import {deleteNotification} from "~portal-engine/scripts/features/notifications/notifications-actions";
import Trans from "~portal-engine/scripts/components/Trans";
import {ReactComponent as WarningIcon} from "~portal-engine/icons/exclamation";
import {ReactComponent as CheckIcon} from "~portal-engine/icons/check";
import {ERROR, SUCCESS} from "~portal-engine/scripts/consts/notification-types";
import {CSSTransition} from 'react-transition-group'

export const mapStateToProps = (state, {id}) => ({
    ...getNotificationById(state, id)
});

export const mapDispatchToProps = (dispatch, {id}) => ({
    onDelete: () => dispatch(deleteNotification({id}))
});

export function Notification({
    message,
    timestamp,
    translation,
    type,
    action = null,
    actionLabel = null,
    actionLabelTranslated = null,
    onDelete = noop
}) {
    const Icon = getIconByNotificationType(type);

    return (
        <CSSTransition
            classNames="notification-"
            in={true}
            appear
            timeout={120}>

            <div className="notification">
                <div className="row flex-nowrap">
                    <div className="col">
                        <div className="notification__content">
                            {timestamp ? (
                                <div className="text-muted text-uppercase small">
                                    {new Date(timestamp * 1000).toLocaleTimeString([],{
                                        hour: '2-digit',
                                        minute: '2-digit',
                                    })}
                                </div>
                            ): null}

                            <div className="position-relative d-flex flex-wrap align-items-center">
                                <div className="flex-grow-1">
                                    <Icon width=".875rem" height=".75rem" className="notification__icon"/>
                                    {message || <Trans t={translation}/>}
                                </div>

                                <div className="flex-shrink-1">
                                    {action != null && (actionLabel != null || actionLabelTranslated != null) && (
                                        <a href={action} className="btn btn-sm btn-outline-secondary ml-2">
                                            {actionLabel || <Trans t={actionLabelTranslated}/>}
                                        </a>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-auto">
                        <button type="button" className="btn btn-link" onClick={() => onDelete()}>
                            <CloseIcon height="1rem"/>
                        </button>
                    </div>
                </div>
            </div>
        </CSSTransition>
    )
}

const IconsByNotificationTypes = {
    [ERROR]: WarningIcon,
    [SUCCESS]: CheckIcon
};
export const getIconByNotificationType = type => IconsByNotificationTypes[type];

export default connect(mapStateToProps, mapDispatchToProps)(Notification);