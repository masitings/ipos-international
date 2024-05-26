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
import {dispatch} from "~portal-engine/scripts/store";
import {setupAsset} from "~portal-engine/scripts/features/asset/asset-actions";
import AssetDetail from "~portal-engine/scripts/features/asset/components/AssetDetail";
import AppRoot from "~portal-engine/scripts/pages/shared/AppRoot";
import {setEndpoint} from "~portal-engine/scripts/utils/fetch";
import {setAPIEndPoint} from "~portal-engine/scripts/utils/api";
import {setupAssetLayout} from "~portal-engine/scripts/features/asset/asset-layout";

init();

let rootElement = document.getElementById('root');

setEndpoint("detail", rootElement.dataset.detailUrl);
setEndpoint("resultList", rootElement.dataset.resultsListUrl);
setAPIEndPoint('folders', rootElement.dataset.foldersUrl);

dispatch(setupAsset({
    assetId: rootElement.dataset.assetId
}));

setupAssetLayout();

const content = (
    <AppRoot>
        <AssetDetail versionsEnabled={!!rootElement.dataset.versionsEnabled}/>
    </AppRoot>
);

ReactDOM.render(content, rootElement);
