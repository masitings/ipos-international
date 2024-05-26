/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {init} from "~portal-engine/scripts/entry";
import React from 'react';
import ReactDOM from 'react-dom';
import AppRoot from "~portal-engine/scripts/pages/shared/AppRoot";

init();

let rootElement = document.getElementById('root');

if (rootElement) {
    ReactDOM.render((
        <AppRoot>
            <div/>
        </AppRoot>
    ), rootElement);
}

