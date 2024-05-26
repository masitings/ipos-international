/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {store} from "~portal-engine/scripts/store";
import {Provider} from "react-redux";
import React from "react";
import NotificationList from "~portal-engine/scripts/features/notifications/components/NotificationList";

export default function AppRoot({children}) {
    return (
        <Provider store={store}>
            {children}

            <NotificationList/>
        </Provider>
    )
}