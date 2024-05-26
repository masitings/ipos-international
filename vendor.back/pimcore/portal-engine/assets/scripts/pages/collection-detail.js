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
import CollectionDetail from "~portal-engine/scripts/features/collections/components/detail/CollectionDetail";
import CollectionDetailActions from "~portal-engine/scripts/features/collections/components/detail/CollectionDetailActions";

init();

let rootElement = document.getElementById('root');

setAPIEndPoint('list', rootElement.dataset.listUrl);
setAPIEndPoint('filters', rootElement.dataset.filtersUrl);
setAPIEndPoint('folders', rootElement.dataset.foldersUrl);
setAPIEndPoint('tags', rootElement.dataset.tagsUrl);
setAPIEndPoint('all-selectable-ids-url', rootElement.dataset.allSelectableIdsUrl);

let collectionId = getConfig('collection.id');

ReactDOM.render((
    <AppRoot>
        <CollectionDetail/>
    </AppRoot>
), rootElement);

let collectionActions = document.getElementById('collection-actions');

ReactDOM.render((
    <Provider store={store}>
        <CollectionDetailActions id={collectionId}/>
    </Provider>
), collectionActions);