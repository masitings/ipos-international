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
import {setAPIEndPoint} from "~portal-engine/scripts/utils/api";
import AppRoot from "~portal-engine/scripts/pages/shared/AppRoot";
import {store} from "~portal-engine/scripts/store";
import {Provider} from "react-redux";
import {getConfig} from "~portal-engine/scripts/utils/general";
import PublicShareDataPoolList
    from "~portal-engine/scripts/features/public-share/components/public-list/PublicShareDataPoolList";
import PublicShareListActions
    from "~portal-engine/scripts/features/public-share/components/public-list/PublicShareListActions";

init();

let rootElement = document.getElementById('root');

setAPIEndPoint('list', rootElement.dataset.listUrl);
setAPIEndPoint('filters', rootElement.dataset.filtersUrl);
setAPIEndPoint('folders', rootElement.dataset.foldersUrl);
setAPIEndPoint('tags', rootElement.dataset.tagsUrl);

ReactDOM.render((
    <AppRoot>
        <PublicShareDataPoolList/>
    </AppRoot>
), rootElement);

let collectionActions = document.getElementById('public-share-actions');

ReactDOM.render((
    <Provider store={store}>
        <PublicShareListActions publicShareHash={getConfig('publicShare.hash')}/>
    </Provider>
), collectionActions);