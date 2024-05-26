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
import {getTaskById} from "~portal-engine/scripts/features/tasks/tasks-selectors";
import {deleteTask, interactedTask} from "~portal-engine/scripts/features/tasks/tasks-actions";
import Trans from "~portal-engine/scripts/components/Trans";
import {ReactComponent as DownloadIcon} from "~portal-engine/icons/download";
import {ReactComponent as EditIcon} from "~portal-engine/icons/edit";
import {ReactComponent as DeleteIcon} from "~portal-engine/icons/trash-alt";
import {TASK_DOWNLOAD_GENERATION, TASK_METADATA_UPDATE, TASK_DELETE_ASSET} from "~portal-engine/scripts/consts/notification-types";
import Progressbar from "~portal-engine/scripts/components/Progressbar";
import {useConfirmModal} from "~portal-engine/scripts/components/modals/ConfirmModal";
import {CSSTransition} from 'react-transition-group'

export const mapStateToProps = (state, {id}) => ({
    ...getTaskById(state, id)
});

export const mapDispatchToProps = (dispatch, {id}) => ({
    onDownload: () => dispatch(interactedTask({id})),
    onDeleteTask: () => dispatch(deleteTask({id}))
});

export function TaskNotification({
    notificationMessage,
    createdAt,
    type,
    subType,
    progress,
    notificationLink = null,
    notificationLinkText = null,
    disableDeleteConfirmation = false,
    onDelete = noop,
    onDeleteTask = noop,
    onDownload = noop
}) {

    const Icon = getIconByNotificationSubType(subType);

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
                        <div className="row row-gutter--2 align-items-end flex-wrap">
                            <div className="col">
                                {createdAt ? (
                                    <div className="text-muted text-uppercase small">
                                        {new Date(createdAt * 1000).toLocaleTimeString([],{
                                            hour: '2-digit',
                                            minute: '2-digit',
                                        })}
                                    </div>
                                ): null}

                                <div className="position-relative">
                                    <Icon width=".875rem" height=".75rem" className="notification__icon"/>
                                    {notificationMessage}
                                </div>
                            </div>

                            {notificationLink && notificationLinkText ? (
                                <div className="col-5 text-right">
                                    <a href={notificationLink} className="btn btn-outline-secondary btn-sm" onClick={() => onDownload()}>
                                        {notificationLinkText}
                                    </a>
                                </div>
                            ) : null}

                            {!notificationLink && notificationLinkText ? (
                                <div className="col-5 text-right">
                                    <a href="#" className="btn btn-outline-secondary btn-sm" onClick={(event) => {event.preventDefault(); onDeleteTask()}}>
                                        {notificationLinkText}
                                    </a>
                                </div>
                            ) : null}
                        </div>
                        <Progressbar className="mt-1" progress={progress} />
                    </div>
                </div>
                <div className="col-auto">
                    <button type="button" className="btn btn-link" onClick={() => (disableDeleteConfirmation ? onDeleteTask() : onDelete())}>
                        <CloseIcon height="1rem"/>
                    </button>
                </div>
            </div>
        </div>
        </CSSTransition>
    )
}

const IconsByNotificationSubTypes = {
    [TASK_DOWNLOAD_GENERATION]: DownloadIcon,
    [TASK_METADATA_UPDATE]: EditIcon,
    [TASK_DELETE_ASSET]: DeleteIcon
};
export const getIconByNotificationSubType = subType => IconsByNotificationSubTypes[subType] ?? EditIcon;

export default connect(mapStateToProps, mapDispatchToProps)(function (props) {
    const progress = props.totalItems ? (100 * props.finishedItems / props.totalItems) : 0;

    const {confirm, confirmModal} = useConfirmModal(props.onDeleteTask, {
        title: <Trans t="task.delete-confirm.title"/>,
        message: <Fragment>
            <Trans t="task.delete-confirm.text"/>
            <div className="mt-2">
                <span className="text-muted small">{ props.notificationMessage }</span>
                <Progressbar progress={progress} />
            </div>
        </Fragment>,
        cancelText: <Trans t="task.delete-confirm.cancel"/>,
        confirmText: <Trans t="task.delete-confirm.confirm"/>,
        confirmStyle: "danger"
    });

    let transformedProps = {
        ...props,
        progress,
        onDelete: confirm
    };

    return (
        <Fragment>
            <TaskNotification {...transformedProps}/>

            {confirmModal}
        </Fragment>
    )
});