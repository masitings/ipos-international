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
import DataObjectList from "~portal-engine/scripts/features/data-pool-list/components/DataPoolList";

import SaveSearchButton from "~portal-engine/scripts/features/search/components/SaveSearchButton";

init({
    searchConfig: {
        autoFocus: true
    }
});

let rootElement = document.getElementById('root');

setAPIEndPoint('list', rootElement.dataset.listUrl);
setAPIEndPoint('filters', rootElement.dataset.filtersUrl);
setAPIEndPoint('folders', rootElement.dataset.foldersUrl);
setAPIEndPoint('tags', rootElement.dataset.tagsUrl);
setAPIEndPoint('all-selectable-ids-url', rootElement.dataset.allSelectableIdsUrl);

ReactDOM.render((
    <AppRoot>
        <DataObjectList/>
    </AppRoot>
), rootElement);

let searchActions = document.getElementById('search-actions');

ReactDOM.render((
    <Provider store={store}>
        <SaveSearchButton/>
    </Provider>
), searchActions);
