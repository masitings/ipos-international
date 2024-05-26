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
import ChartContainer from "./ChartContainer";
import ResultTable from "./ResultTable";

export default function ResultContainer(props) {

    function filterForEmptyFilters(filters) {
        return filters.filter(item => (item.field && item.operator))
    }

    return (
        <div>
            { props.showChart && props.chartType &&
                <div className={props.showTable ? "mb-5" : ""}>
                    <ChartContainer
                        configurationId={props.configurationId}
                        dataUrl={props.dataUrl}
                        selectedDataSource={props.selectedDataSource}
                        rows={props.rows}
                        columns={props.columns}
                        filters={JSON.stringify(filterForEmptyFilters(props.filters))}
                        fieldSettings={JSON.stringify(props.fieldSettings)}
                        chartType={props.chartType}
                    />
                </div>
            }

            { props.showTable &&
                <div>
                    <ResultTable
                        configurationId={props.configurationId}
                        dataUrl={props.dataUrl}
                        selectedDataSource={props.selectedDataSource}
                        statisticMode={props.statisticMode}
                        rows={props.rows}
                        columns={props.columns}
                        filters={JSON.stringify(props.filters)}
                        fieldSettings={JSON.stringify(props.fieldSettings)}
                    />
                </div>
            }

        </div>
    );
}