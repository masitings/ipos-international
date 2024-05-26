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
import DataObjectDetail from "~portal-engine/scripts/features/data-objects/components/DataObjectDetail";
import {dispatch} from "~portal-engine/scripts/store";
import {setEndpoint} from "~portal-engine/scripts/utils/fetch";
import {setupDataObject} from "~portal-engine/scripts/features/data-objects/data-object-actions";
import {setupDataObjectLayout} from "~portal-engine/scripts/features/data-objects/object-layout";

init();

const rootElement = document.getElementById('root');

setEndpoint("detail", rootElement.dataset.detailUrl);
setEndpoint("resultList", rootElement.dataset.resultsListUrl);
setEndpoint("versionHistory", rootElement.dataset.versionHistoryUrl);
setEndpoint("versionComparison", rootElement.dataset.versionComparisonUrl);

dispatch(setupDataObject(
    rootElement.dataset.dataObjectId,
    rootElement.dataset.dataPoolId,
    rootElement.dataset.versionsEnabled)
);

setupDataObjectLayout();

const content = (
    <AppRoot>
        <DataObjectDetail/>
    </AppRoot>
);

ReactDOM.render(content, rootElement);