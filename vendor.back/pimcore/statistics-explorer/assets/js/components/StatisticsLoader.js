/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, { useState, useEffect } from 'react';
import ResultContainer from "./ResultContainer";
import handleErrors from "./fetchErrorHandler";
import LoadingSpinner from "./LoadingSpinner";


export default function StatisticsLoader(props) {
    const [config, setConfig] = useState(null);

    useEffect(() => {
        fetch(props.loadConfigurationUrl + "?" + new URLSearchParams({'configurationId': props.configId}))
            .then(handleErrors)
            .then(res => res.json())
            .then(
                (result) => {
                    const configuration = JSON.parse(result.configuration);
                    setConfig(configuration);
                }
            )
            .catch(error => {
                console.error(error);
                toast.error('Error loading configuration.', {autoClose: false});
            })

    }, []);


    if(config) {
        return (
            <ResultContainer
                configurationId={props.configId}
                showTable={config.showTable}
                showChart={config.showChart}
                chartType={config.chartType}
                dataUrl={props.resultDataUrl}
                selectedDataSource={config.selectedDataSource}
                statisticMode={config.statisticMode}
                rows={config.rows}
                columns={config.columns}
                filters={config.filters}
                fieldSettings={config.fieldSettings}
            />
        );

    } else {
        return (<LoadingSpinner />);
    }
}