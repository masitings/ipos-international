/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import '../css/app.css';

import React from 'react';
import ReactDOM from 'react-dom';
import StatisticsLoader from "./components/StatisticsLoader";

let statisticsExplorerConfig = window.statisticsExplorerConfig || {};

let urls = ['loadConfigurationUrl', 'resultDataUrl', 'translationsUrl'];
let configValid = true;

for (const url of urls) {
    if(!statisticsExplorerConfig[url]) {
        console.error('Missing config option `' + url + '`.');
        configValid = false;
    }
}

if(configValid) {

    const elements = document.getElementsByClassName('statistics-container');
    for (let element of elements) {

        const loader = (
            <StatisticsLoader
                configId={element.dataset.configId}
                loadConfigurationUrl={statisticsExplorerConfig.loadConfigurationUrl}
                resultDataUrl={statisticsExplorerConfig.resultDataUrl}
            />

        );

        ReactDOM.render(
            loader,
            element
        );

    }
}
