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
import StatisticsConfigContainer from "./components/StatisticsConfigContainer";
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.min.css';

const targetDomElement = document.getElementById('statistics-explorer');

let statisticsExplorerConfig = window.statisticsExplorerConfig || {};

let urls = ['dataSourceListUrl', 'fieldsListUrl', 'resultDataUrl', 'loadFieldSettingsUrl', 'loadConfigurationListUrl', 'saveConfigurationUrl', 'loadConfigurationUrl', 'deleteConfigurationUrl', 'translationsUrl'];
let configValid = true;

for (const url of urls) {
    if(!statisticsExplorerConfig[url]) {
        console.error('Missing config option `' + url + '`.');
        toast.error('Missing config option `' + url + '`.');
        configValid = false;
    }
}

let element = null;

if(configValid) {
    element = (
        <div>
            <StatisticsConfigContainer
                dataSourceListUrl={statisticsExplorerConfig.dataSourceListUrl}
                fieldsListUrl={statisticsExplorerConfig.fieldsListUrl}
                resultDataUrl={statisticsExplorerConfig.resultDataUrl}
                loadFieldSettingsUrl={statisticsExplorerConfig.loadFieldSettingsUrl}
                loadConfigurationListUrl={statisticsExplorerConfig.loadConfigurationListUrl}
                saveConfigurationUrl={statisticsExplorerConfig.saveConfigurationUrl}
                loadConfigurationUrl={statisticsExplorerConfig.loadConfigurationUrl}
                deleteConfigurationUrl={statisticsExplorerConfig.deleteConfigurationUrl}
            />
            <ToastContainer
                limit={5}
            />
        </div>
    );

} else {
    element = (
        <div>
            <ToastContainer
                limit={5}
            />
        </div>
    );
}

ReactDOM.render(
    element,
    targetDomElement
);

