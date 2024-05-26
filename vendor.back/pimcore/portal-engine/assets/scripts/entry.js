/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from 'react';
import {init as initMainNav} from "~portal-engine/scripts/pages/shared/main-nav";
import {init as initSearch} from "~portal-engine/scripts/pages/shared/global-search";

export function init({
    searchConfig = {},
    mainNavConfig = {}
} = {}) {
    initSearch(searchConfig);

    initMainNav(mainNavConfig);
}
