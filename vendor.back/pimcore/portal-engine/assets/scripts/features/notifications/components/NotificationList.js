/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState, useEffect} from "react";
import {connect} from "react-redux";
import Trans from "~portal-engine/scripts/components/Trans";
import {ReactComponent as AngleDown} from "~portal-engine/icons/angle-down";
import {ReactComponent as BellIcon} from "~portal-engine/icons/bell";
import {getAllNotifications} from "~portal-engine/scripts/features/notifications/notifications-selectors";
import Notification from "~portal-engine/scripts/features/notifications/components/Notification";
import TaskNotification from "~portal-engine/scripts/features/notifications/components/TaskNotification";
import {Collapse} from "react-collapse";
import {noop} from "~portal-engine/scripts/utils/utils";
import {clearAllNotifications} from "~portal-engine/scripts/features/notifications/notifications-actions";
import {fetchTasks} from "~portal-engine/scripts/features/tasks/tasks-actions";
import {getFetchingState, getAllTasks} from "~portal-engine/scripts/features/tasks/tasks-selectors";
import {NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import {TASK} from "~portal-engine/scripts/consts/notification-types";
import {CSSTransition} from 'react-transition-group'

export const mapDispatchToProps = (dispatch) => ({
    fetchTasks: () => dispatch(fetchTasks()),
    onClose: () => dispatch(clearAllNotifications())
});

export const mapStateToProps = (state) => ({
    notifications: getAllNotifications(state),
    tasks: getAllTasks(state),
    taskFetchingState: getFetchingState(state)
});

export function Notifications({
    notifications = {},
    tasks = {},
    taskFetchingState,
    onClose = noop,
    fetchTasks = noop
}) {

    useEffect(function () {
        if (taskFetchingState === NOT_ASKED) {
            fetchTasks();
        }
    }, []);

    const [isOpen, setOpen] = useState(false);

    const handleClose = () => {
        onClose();
        setOpen(false);
    };

    let notificationList = [...Object.keys(tasks).map(i => tasks[i]), ...Object.keys(notifications).map(i => notifications[i])];
    notificationList.sort((a, b) => a.timestamp - b.timestamp);

    const previousIds = notificationList.slice(0, -1);
    const latestId = notificationList.slice(-1)[0];

    return (
        <CSSTransition
            classNames="notification-list-"
            in={notificationList.length > 0}
            unmountOnExit={true}
            timeout={200}
            appear
        >
            <div className={`notification-list ${isOpen ? 'is-open' : ''}`}>
                <div className="notification-list__header font-weight-bold position-relative" onClick={() => setOpen(!isOpen)}>
                    <div className="row row-gutter--2 align-items-center">
                        <div className="col-auto">
                            <BellIcon width=".875rem" height=".875rem"/>
                        </div>
                        <div className="col">
                            {notificationList.length > 1 ? (
                                `${notificationList.length} ` /*force whitespace*/
                            ) : null}

                            <Trans t="notifications.title"/>
                        </div>
                    </div>


                    <div className="notification-list__header__collapse">
                        {notificationList.length === 1 ? null : (
                            <AngleDown width="10"/>
                        )}
                    </div>
                </div>

                <div className="notification-list__body">
                    <ul className="list-unstyled mb-0">
                        <Collapse isOpened={isOpen}>
                            {previousIds.map(id => (
                                <li key={id.id}>
                                    {id.type === TASK ? (
                                        <TaskNotification id={id.id} />
                                    ) : <Notification id={id.id}/>}
                                </li>
                            ))}
                        </Collapse>

                        {
                            notificationList.length === 0 ? null : (
                                <li key={latestId.id}>
                                    {latestId.type === TASK ? (
                                        <TaskNotification id={latestId.id} />
                                    ) : <Notification id={latestId.id}/>}
                                </li>
                            )
                        }
                    </ul>
                </div>
            </div>
        </CSSTransition>
    );
}

export default connect(mapStateToProps, mapDispatchToProps)(Notifications)
