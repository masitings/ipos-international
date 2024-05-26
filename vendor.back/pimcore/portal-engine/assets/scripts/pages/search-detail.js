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
import {SearchOverview} from "~portal-engine/scripts/features/search/components/SearchOverview";

init();

let rootElement = document.getElementById('root');

ReactDOM.render((
    <AppRoot>
        <SearchOverview/>
    </AppRoot>
), rootElement);