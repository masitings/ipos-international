/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import ReactDOM from "react-dom";
import {Provider} from "react-redux";
import {store} from "~portal-engine/scripts/store";
import Typeahead from "~portal-engine/scripts/features/search/components/Typeahead";
import React from "react";

export function init({
    autoFocus = false
} = {}) {
    let searchElement = document.getElementById('typeahead-search');

    if (searchElement) {
        ReactDOM.render((
            <Provider store={store}>
                <Typeahead autoFocus={autoFocus}/>
            </Provider>
        ), searchElement);
    }
}