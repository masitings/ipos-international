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
import LoadingSpinner from "./LoadingSpinner";
import handleErrors from './fetchErrorHandler';

import { LineChart, Line, BarChart, Bar, AreaChart, Area, linearGradient, PieChart, Pie, Cell, XAxis, YAxis, Tooltip, ResponsiveContainer, Legend, CartesianGrid } from 'recharts';

const chartColors = [
    '#a50026',
    '#d73027',
    '#f36d42',
    '#fbae61',
    '#fee090',
    '#abd9e9',
    '#74acd0',
    '#4576b5',
    '#08306b',
    '#54278f',
    '#7a0177',
    '#c994c7',
    '#a1d99b',
    '#41ab5d',
    '#238b45',
    '#00441b'
];



// function CustomizedAxisTick(props) {
//     const {
//         x, y, stroke, payload,
//     } = props;
//
//     return (
//         <g transform={`translate(${x},${y})`}>
//             <text x={0} y={0} dy={16} textAnchor="end" fill="#666" transform="rotate(-35)">{payload.value}</text>
//         </g>
//     );
// }


class ChartContainer extends React.Component {

    constructor (props) {
        super(props);

        this.isLoading = false;
        this.state = {
            error: null,
            isLoaded: false,
            showLoadingSpinner: false,
            chart: {
                data: [],
                columnHeaders: []
            },
        };
    }

    loadData(rawRows, rawColumns, dataUrl, fieldSettings, selectedDataSource, filters, configurationId) {
        if(this.isLoading) {
            this.controller.abort();
        }

        this.isLoading = true;
        this.controller = new AbortController();
        this.signal = this.controller.signal;


        const rows = rawRows.map(item => item.value);
        const columns = rawColumns.map(item => item.value);

        if(rows.length > 0) {

            this.setState({showLoadingSpinner: true});

            const urlParams = new URLSearchParams({
                rows: rows,
                columns: columns,
                dataSource: selectedDataSource.value,
                fieldSettings: fieldSettings,
                filters: filters,
                configurationId: configurationId,
                chartData: true
            });

            if(this.loadingTimeout) {
                clearTimeout(this.loadingTimeout);
            }

            this.loadingTimeout = setTimeout(function() {
                    fetch(dataUrl + "?" + urlParams, {
                        signal: this.signal
                    })
                        .then(handleErrors)
                        .then(response => response.json())
                        .then((result) => {

                                const stateUpdate = {
                                    chart: {
                                        data: result.data,
                                        columnHeaders: result.columnHeaders
                                    },
                                    showLoadingSpinner: false
                                };

                                this.setState(stateUpdate);
                            },
                        )
                        .catch((error) => {
                            console.error(error);
                            if (error.name !== 'AbortError') {
                                this.setState({
                                    chart: {
                                        data: [],
                                        columnHeaders: []
                                    },
                                    showLoadingSpinner: false
                                });
                            }
                            this.isLoading = false;
                        });
                }.bind(this),
                800
            );

        } else {
            this.setState({
                chart: {
                    data: [],
                    columnHeaders: []
                },
                showLoadingSpinner: false
            });
        }
    }

    componentDidMount() {
        this.loadData(this.props.rows, this.props.columns, this.props.dataUrl,this.props.fieldSettings, this.props.selectedDataSource, this.props.filters, this.props.configurationId);
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (this.props.columns !== prevProps.columns || this.props.rows !== prevProps.rows || this.props.fieldSettings !== prevProps.fieldSettings || this.props.filters !== prevProps.filters || this.props.selectedDataSource.value !== prevProps.selectedDataSource.value) {
            this.loadData(this.props.rows, this.props.columns, this.props.dataUrl,this.props.fieldSettings, this.props.selectedDataSource, this.props.filters, this.props.configurationId);
        }
    }

    renderChart() {

        if (this.props.chartType === 'Line') {
            return this.renderChartTypeLine();
        } else if (this.props.chartType === 'Bar') {
            return this.renderChartTypeBar();
        } else if (this.props.chartType === 'Area') {
            return this.renderChartTypeArea();
        } else if (this.props.chartType === 'Pie') {
            return this.renderChartTypePie();
        }
        return null;

    }

    renderChartTypeLine() {
        return (
            <LineChart data={this.state.chart.data}>
                <CartesianGrid strokeDasharray="3 3"/>
                {
                    this.state.chart.columnHeaders.map((entry, index) => (
                            <Line key={index} name={entry.label} dataKey={entry.dataKey}
                                  stroke={chartColors[index]}/>
                        )
                    )
                }
                {/*<XAxis dataKey="label" height={60} tick={<CustomizedAxisTick />} />*/}
                <XAxis dataKey="label"/>
                <YAxis/>
                <Tooltip/>
                <CartesianGrid strokeDasharray="3 3" />
                <Legend/>
            </LineChart>
        );
    }

    renderChartTypeBar() {
        return (
            <BarChart data={this.state.chart.data}>
                <CartesianGrid strokeDasharray="3 3"/>
                <defs>
                    {
                        this.state.chart.columnHeaders.map((entry, index) => (
                            <linearGradient key={index}  id={'color' + (index % chartColors.length)} x1="0" y1="0" x2="0" y2="1">
                                <stop offset="10%" stopColor={chartColors[index % chartColors.length]} stopOpacity={0.8}/>
                                <stop offset="95%" stopColor={chartColors[index % chartColors.length]} stopOpacity={0.1}/>
                            </linearGradient>
                        ))
                    }
                </defs>

                {
                    this.state.chart.columnHeaders.map((entry, index) => (
                            <Bar key={index} name={entry.label} dataKey={entry.dataKey}
                                 stroke={chartColors[index % chartColors.length]} fillOpacity={1} fill={`url(#color${(index % chartColors.length)})`} />
                        )
                    )
                }
                <XAxis dataKey="label"/>
                <YAxis/>
                <Tooltip cursor={{fill: 'transparent'}}/>
                <Legend/>
            </BarChart>
        );
    }

    renderChartTypeArea() {
        return (
            <AreaChart data={this.state.chart.data}>
                <CartesianGrid strokeDasharray="3 3"/>
                <defs>
                    {
                        this.state.chart.columnHeaders.map((entry, index) => (
                            <linearGradient key={index}  id={'color' + (index % chartColors.length)} x1="0" y1="0" x2="0" y2="1">
                                <stop offset="10%" stopColor={chartColors[index % chartColors.length]} stopOpacity={0.8}/>
                                <stop offset="95%" stopColor={chartColors[index % chartColors.length]} stopOpacity={0.1}/>
                            </linearGradient>
                        ))
                    }
                </defs>

                {
                    this.state.chart.columnHeaders.map((entry, index) => (
                            <Area key={index} name={entry.label} type="monotone" dataKey={entry.dataKey} stroke={chartColors[index % chartColors.length]} fillOpacity={1} fill={`url(#color${(index % chartColors.length)})`} />
                        )
                    )
                }
                <XAxis dataKey="label"/>
                <YAxis/>
                <Tooltip/>
                <Legend/>
            </AreaChart>
        );
    }

    renderChartTypePie() {
        const fixedColorLabel = ({index, x, y, midAngle, value}) => {
            const topOffset = 10;
            const middleOffset = 20;
            let alignment = 'bottom';
            let textAnchor = midAngle < 90 - topOffset || midAngle > 270 + topOffset ? 'start' : 'end';
            let finalX = x;
            let finalY = y;

            if((midAngle >= 90 - topOffset && midAngle <= 90 + topOffset) || (midAngle >= 270 - topOffset && midAngle <= 270 + topOffset)) {
                textAnchor = 'middle';
            }
            if(midAngle >= 270 - topOffset && midAngle <= 270 + topOffset) {
                finalY = finalY + 15;
            }

            if((midAngle >= 360 - middleOffset || midAngle <= 0 + middleOffset) || (midAngle >= 180 - middleOffset && midAngle <= 180 + middleOffset)) {
                alignment = 'central';
            }


            return (
                <text x={finalX} y={finalY} fill={chartColors[index % chartColors.length]} alignmentBaseline={alignment} textAnchor={textAnchor} >
                    {value}
                </text>
            );
        };

        return (
            <PieChart>
                <defs>
                    {
                        this.state.chart.data.map((entry, index) => (
                            <linearGradient key={index}  id={'color' + (index % chartColors.length)} x1="0" y1="0" x2="0" y2="1">
                                <stop offset="5%" stopColor={chartColors[index % chartColors.length]} stopOpacity={1}/>
                                <stop offset="95%" stopColor={chartColors[index % chartColors.length]} stopOpacity={0.5}/>
                            </linearGradient>
                        ))
                    }
                </defs>

                {
                    this.state.chart.columnHeaders.map((entry, index) => (
                        <Pie key={index} data={this.state.chart.data} dataKey={entry.dataKey}
                             nameKey={ (entry.dataKey === 'value' ? 'label' : entry.dataKey + '__label' )  }
                             label={fixedColorLabel} startAngle={45} endAngle={405} >

                            {
                                this.state.chart.data.map((entry, index) =>
                                    <Cell key={`cell-${index}`} fillOpacity={1} fill={`url(#color${(index % chartColors.length)})`} />
                                )
                            }

                        </Pie>

                    ))
                }

                <Tooltip/>
                <Legend/>
            </PieChart>
        );
    }


    render () {
        if(this.props.chartType) {
            if(this.state.showLoadingSpinner) {
                return <LoadingSpinner/>;
            }

            if(this.state.chart.data) {
                if (this.state.chart.data.length < 1) {
                    return (
                        <div className="alert alert-warning" role="alert">
                            No results for chart.
                        </div>
                    );
                }
                return (
                    <div>
                        <ResponsiveContainer height={400}>
                            { this.renderChart() }
                        </ResponsiveContainer>
                    </div>
                );
            }

        }

        return null;
    }

}


export default ChartContainer;