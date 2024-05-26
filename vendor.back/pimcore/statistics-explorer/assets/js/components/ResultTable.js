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

class ResultTable extends React.Component {

    constructor(props) {
        super(props);

        this.isLoading = false;
        this.state = {
            content: '',
            showLoadingSpinner: false
        };
    }

    loadData(selectedDataSource, statisticMode, rawRows, rawColumns, dataUrl, fieldSettings, filters, configurationId) {
        if(this.isLoading) {
            this.controller.abort();
        }

        this.isLoading = true;
        this.controller = new AbortController();
        this.signal = this.controller.signal;

        const rows = rawRows.map(item => item.value);
        const columns = rawColumns.map(item => item.value);

        if((statisticMode === 'statistic' && rows.length > 0) || (statisticMode === 'list' && columns.length > 0)) {

            this.setState({showLoadingSpinner: true});

            if(this.loadingTimeout) {
                clearTimeout(this.loadingTimeout);
            }

            const urlParams = new URLSearchParams({
                dataSource: selectedDataSource.value,
                statisticMode: statisticMode,
                rows: rows,
                columns: columns,
                fieldSettings: fieldSettings,
                configurationId: configurationId,
                filters: filters
            });

            this.loadingTimeout = setTimeout(function() {
                    fetch(dataUrl + "?" + urlParams, {
                        signal: this.signal
                    })
                        .then(handleErrors)
                        .then(response => response.text())
                        .then((responseText) => {
                            this.setState({content: responseText, showLoadingSpinner: false})
                            this.isLoading = false;
                        })
                        .catch((error) => {
                            if (error.name !== 'AbortError') { // handle abort()
                                const content = '<div class="alert alert-warning" role="alert">' + error + '</div>';
                                this.setState({content: content, showLoadingSpinner: false});
                            }
                            this.isLoading = false;
                        })
                    ;
                }.bind(this),
                800
            );
        } else {
            this.setState({content: '', showLoadingSpinner: false});
        }
    }

    componentDidMount() {
        this.loadData(this.props.selectedDataSource, this.props.statisticMode, this.props.rows, this.props.columns, this.props.dataUrl,this.props.fieldSettings, this.props.filters, this.props.configurationId);
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (this.props.columns !== prevProps.columns || this.props.rows !== prevProps.rows || this.props.fieldSettings !== prevProps.fieldSettings || this.props.filters !== prevProps.filters || this.props.selectedDataSource.value !== prevProps.selectedDataSource.value || this.props.statisticMode !== prevProps.statisticMode) {
            this.loadData(this.props.selectedDataSource, this.props.statisticMode, this.props.rows, this.props.columns, this.props.dataUrl,this.props.fieldSettings, this.props.filters, this.props.configurationId);
        }
    }

    render () {
        if(this.state.showLoadingSpinner) {
            return <LoadingSpinner />;
        } else {
            return <div dangerouslySetInnerHTML={ {__html: this.state.content} } />;
        }
    }
}


export default ResultTable;