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
import Form from 'react-bootstrap/Form';
import Card from 'react-bootstrap/Card';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import Button from 'react-bootstrap/Button';
import Accordion from 'react-bootstrap/Accordion';
import ResultContainer from './ResultContainer';
import Filters from './Filters';
import Select from 'react-select';
import MultiSelectSort from './MultiSelectSort';
import DataSourceFieldSettings from './DataSourceFieldSettings';
import { toast } from 'react-toastify';
import handleErrors from './fetchErrorHandler';
import translate from './TranslationService';


const initialState = {
    changed: false,
    indices: [],
    selectedDataSource: {},
    fields: [],
    filterOperators: {},
    statisticMode: 'statistic',
    columns: [],
    rows: [],
    fieldSettings: {},
    chartType: null,
    filters: [],
    showTable: true,
    showChart: false,
    name: '',
    configurationId: null,
    configurationList: [],
    sharedUsers: [],
    sharedRoles: [],
    ownerShip: 'owner',
    selectedConfigurationToLoad: null,
    // otherUsers: {
    //     roles: [],
    //     users: []
    // }
};

class StatisticsConfigContainer extends React.Component {

    constructor (props) {
        super(props);

        this.state = { ... initialState};

        this.onDataSourceSelect = this.onDataSourceSelect.bind(this);
        this.onStatisticModeSelect = this.onStatisticModeSelect.bind(this);
        this.onChartTypeSelect = this.onChartTypeSelect.bind(this);
        this.updateFieldSettings = this.updateFieldSettings.bind(this);
        this.updateFilters = this.updateFilters.bind(this);
        this.saveConfiguration = this.saveConfiguration.bind(this);
        this.deleteConfiguration = this.deleteConfiguration.bind(this);
        this.loadConfiguration = this.loadConfiguration.bind(this);
        this.updateConfigurationToLoad = this.updateConfigurationToLoad.bind(this);
        this.updateName = this.updateName.bind(this);
        this.resetState = this.resetState.bind(this);
    }

    onDataSourceSelect(value) {
        this.setState({
            changed: true,
            selectedDataSource: value,
            fields: [],
            filterOperators: {},
            statisticMode: 'statistic',
            columns: [],
            rows: [],
            fieldSettings: {},
            chartType: null,
            filters: [],
            showTable: true,
            showChart: false,
        });
    }

    resetState() {
        this.setState({... initialState});
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        this.loadConfigurationList();
    }

    onVisualizationChanges(field, event) {
        this.setState(function(field, value, state, props) {
            return {
                changed: true,
                [field]: value
            };
        }.bind(this, field, event.target.checked));
    }

    onChartTypeSelect(event) {
        this.setState({
            changed: true,
            chartType: event.target.value
        });
    }

    onStatisticModeSelect(value) {
        this.setState(function(state, props) {
            return {
                changed: true,
                statisticMode: value.value,
                columns: [],
                rows: [],
                possibleFields: state.fields,
                showTable: true,
                showChart: false
            };
        });
    }

    updatePossibleFields(type, values) {
        let possibleFields = this.state.fields;
        const allSelectedFields = [].concat(values, (type === 'rows' ? this.state.columns: this.state.rows));

        return possibleFields.filter( ( el ) => !allSelectedFields.some( selectedFieldsElement => el.value == selectedFieldsElement.value ));
    }

    onFieldSelect(type, values) {
        let valueArray = values || [];
        this.setState(function(state, props) {
            return {
                changed: true,
                [type]: valueArray,
                possibleFields: this.updatePossibleFields(type, valueArray)
            };
        });
    }

    updateFieldSettings(name, values) {
        this.setState(function(state, props) {
            let fieldSettings = state.fieldSettings;
            const hasChanged = state.changed || JSON.stringify((fieldSettings[name] || '')) !== JSON.stringify((values || ''));

            if(values) {
                fieldSettings[name] = values;
            } else {
                fieldSettings[name] = undefined;
            }

            return {
                changed: hasChanged,
                fieldSettings: fieldSettings
            };
        }.bind(this));
    }

    updateFilters(action, event) {
        if(action === 'update') {
            const target = {
                name: event.target.name,
                value: event.target.value,
            };

            this.setState(function(action, event, state, props) {

                let filters = state.filters;

                const name = event.name;
                const value = event.value;

                const nameParts = name.split('.');
                const index = nameParts[0];
                const fieldName = nameParts[1];

                if(filters[index]) {
                    filters[index][fieldName] = value;
                }

                return {
                    changed: true,
                    filters: filters
                };
            }.bind(this, action, target));
        }

        if(action === 'delete') {
            this.setState(function(action, index, state, props) {
                let filters = state.filters;
                filters.splice(index, 1);
                return {
                    changed: true,
                    filters: filters
                };
            }.bind(this, action, event));
        }

        if(action === 'add') {
            this.setState(function(state, props) {
                let filters = state.filters;
                filters.push({
                    field: '',
                    operator: '',
                    filter: ''
                });
                return {
                    changed: true,
                    filters: filters
                };
            }.bind(this));
        }

    }

    updateName(event) {
        this.setState({
            changed: true,
            name: event.target.value
        });
    }

    updateSharingOptions(type, values) {
        let valueArray = values || [];
        this.setState(function(state, props) {
            return {
                changed: true,
                [type]: valueArray,
            };
        });
    }

    saveConfiguration() {

        const configuration = {
            showTable: this.state.showTable,
            showChart: this.state.showChart,
            chartType: this.state.chartType,
            selectedDataSource: this.state.selectedDataSource,
            statisticMode: this.state.statisticMode,
            rows: this.state.rows,
            columns: this.state.columns,
            filters: this.state.filters,
            fieldSettings: this.state.fieldSettings
        };

        const params = new URLSearchParams({
            configId: this.state.configurationId,
            name: this.state.name,
            configuration: JSON.stringify(configuration),
            sharedUsers: this.state.sharedUsers.map(item => item.value),
            sharedRoles: this.state.sharedRoles.map(item => item.value)
        });

        fetch(this.props.saveConfigurationUrl, {
                method: 'post',
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: params.toString()
            })
            .then(handleErrors)
            .then(response => response.json())
            .then(function(data) {
                this.setState({
                    configurationId: data.configurationId,
                    ownerShip: data.ownerShip,
                    changed: false
                });
                this.loadConfigurationList();
                toast.success("Configuration saved successfully.");
            }.bind(this))
            .catch(error => {
                console.error(error);
                toast.error('Error saving configuration.', {autoClose: false});
            })
        ;
    }

    loadConfigurationList() {
        fetch(this.props.loadConfigurationListUrl)
            .then(handleErrors)
            .then(res => res.json())
            .then(
                (result) => {

                    this.setState({
                        configurationList: result.options
                    });
                }
            )
            .catch(error => {
                console.error(error);
                toast.error('Error loading saved configurations.', {autoClose: false});
            })
        ;
    }

    loadConfiguration() {

        fetch(this.props.loadConfigurationUrl+ "?" + new URLSearchParams({configurationId: this.state.selectedConfigurationToLoad.value}))
            .then(handleErrors)
            .then(res => res.json())
            .then(
                function(result) {

                    const config = JSON.parse(result.configuration);
                    const sharedRoles = this.state.otherUsers.roles ? this.state.otherUsers.roles.filter(item => result.shares.role && result.shares.role.includes(item.value)) : [];
                    const sharedUsers = this.state.otherUsers.users ? this.state.otherUsers.users.filter(item => result.shares.user && result.shares.user.includes(item.value)) : [];

                    this.setState({
                        configurationId: result.configurationId,
                        name: result.name,
                        changed: false,
                        showTable: config.showTable,
                        showChart: config.showChart,
                        chartType: config.chartType,
                        selectedDataSource: config.selectedDataSource,
                        statisticMode: config.statisticMode,
                        rows: config.rows,
                        columns: config.columns,
                        filters: config.filters,
                        fieldSettings: config.fieldSettings,
                        sharedUsers: sharedUsers,
                        sharedRoles: sharedRoles,
                        ownerShip: result.ownerShip,
                        selectedConfigurationToLoad: null
                    });
                    document.body.scrollTop = 0; // For Safari
                    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
                }.bind(this)
            )
            .catch(error => {
                console.error(error);
                toast.error('Error loading configuration.', {autoClose: false});
            })
        ;
    }

    deleteConfiguration() {
        fetch(this.props.deleteConfigurationUrl+ "?" + new URLSearchParams({configurationId: this.state.configurationId}))
            .then(handleErrors)
            .then(res => res.json())
            .then(
                (result) => {
                    toast.success("Configuration deleted successfully.");
                    this.resetState();
                }
            )
            .catch(error => {
                console.error(error);
                toast.error('Error deleting configuration.', {autoClose: false});
            })
        ;
    }

    updateConfigurationToLoad(value) {
        this.setState({selectedConfigurationToLoad: value});
    }

    componentDidUpdate(prevProps, prevState) {

        let loadedConfigurationHasChanged = false;
        if(this.state.configurationId !== prevState.configurationId) {
            loadedConfigurationHasChanged = true;
        }

        if(loadedConfigurationHasChanged || this.state.selectedDataSource.value !== prevState.selectedDataSource.value) {

            if(this.state.selectedDataSource.value) {
                fetch(this.props.fieldsListUrl + "?" + new URLSearchParams({dataSource: this.state.selectedDataSource.value }))
                    .then(handleErrors)
                    .then(res => res.json())
                    .then(
                        (result) => {
                            this.setState({
                                fields: result.fields,
                                possibleFields: result.fields,
                                filterOperators: result.operators,
                            });

                            //if config has not changed, reset rows&columns
                            if(loadedConfigurationHasChanged) {
                                let possibleFields = result.fields;
                                const allSelectedFields = [].concat(this.state.columns, this.state.rows);

                                possibleFields = possibleFields.filter( ( el ) => !allSelectedFields.some( selectedFieldsElement => el.value == selectedFieldsElement.value ));
                                this.setState({
                                    possibleFields: possibleFields
                                });

                            } else {
                                this.setState({
                                    columns: [],
                                    rows: [],
                                    fieldSettings: {}
                                });
                            }
                        }
                    )
                    .catch(error => {
                        console.error(error);
                        toast.error('Error loading possible fields of data source.', {autoClose: false});
                    })
                ;

            } else {
                this.setState({
                    fields: [],
                    possibleFields: [],
                    filterOperators: [],
                    columns: [],
                    rows: [],
                    fieldSettings: {}
                });
            }

        }
    }


    componentDidMount() {

        fetch(this.props.dataSourceListUrl)
            .then(handleErrors)
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        dataSources: result.dataSources,
                        otherUsers: result.otherUsers
                    });
                },
            )
            .catch(error => {
                console.error(error);
                toast.error('Error loading data sources.', {autoClose: false});
            })
        ;

        this.loadConfigurationList();
    }


    render() {
        const statisticModes = [{
                label: translate('lbl_mode_statistic'),
                value: 'statistic'
            },{
                label: translate('lbl_mode_list'),
                value: 'list'
        }];
        const currentStatisticMode = statisticModes.find(element => element.value === this.state.statisticMode);

        return (
            <Row>
                <Col xs={12} md={4}>
                    <Card>
                        <Card.Header>
                            <span className="align-middle">
                                {translate('ttl_configuration')} <strong>{this.state.name}</strong> { this.state.changed ? '*unsaved*' : '' }
                            </span>
                            <Button size="sm" variant="outline-secondary" className="float-right" onClick={this.resetState}>{translate('ttl_start_new')}</Button>
                        </Card.Header>
                        <Card.Body>

                            <Form.Group>
                                <Form.Label>{translate('lbl_datasource')}</Form.Label>
                                <Select options={this.state.dataSources} value={this.state.selectedDataSource} className="w-100 mr-2" onChange={this.onDataSourceSelect} />
                            </Form.Group>

                            {this.state.selectedDataSource.value &&

                            <div>

                                <Form.Group>
                                    <Form.Label>{translate('lbl_mode')}</Form.Label>
                                    <Select options={statisticModes}
                                            className="filter-select" classNamePrefix="filter-select"
                                            name="statisticMode"
                                            onChange={this.onStatisticModeSelect}
                                            value={currentStatisticMode}
                                    />
                                </Form.Group>

                                {(this.state.statisticMode === 'statistic') &&
                                    <Form.Group>
                                        <Form.Label>{translate('lbl_rows')}</Form.Label>
                                        <MultiSelectSort options={this.state.possibleFields} isMulti={true}
                                                className="filter-select" classNamePrefix="filter-select"
                                                name="rows"
                                                placeholder={translate('lbl_do_select')}
                                                onChange={this.onFieldSelect.bind(this, 'rows')}
                                                value={this.state.rows}
                                        />

                                    </Form.Group>
                                }
                                <Form.Group>
                                    <Form.Label>{translate('lbl_columns')}</Form.Label>
                                    <MultiSelectSort options={this.state.possibleFields} isMulti={true}
                                            className="filter-select" classNamePrefix="filter-select"
                                            name="columns"
                                            placeholder={translate('lbl_do_select')}
                                            onChange={this.onFieldSelect.bind(this, 'columns')}
                                            value={this.state.columns}
                                    />
                                </Form.Group>

                                <Filters
                                    filters={this.state.filters}
                                    fields={this.state.fields}
                                    filterOperators={this.state.filterOperators}
                                    onUpdate={this.updateFilters}
                                />

                                <DataSourceFieldSettings
                                    selectedDataSource={this.state.selectedDataSource}
                                    statisticMode={this.state.statisticMode}
                                    rows={this.state.rows.slice()}
                                    columns={this.state.columns.slice()}
                                    loadFieldSettingsUrl={this.props.loadFieldSettingsUrl}
                                    fieldSettings={this.state.fieldSettings}
                                    onUpdateSettings={this.updateFieldSettings}
                                />

                                {(this.state.statisticMode === 'statistic') &&
                                    <Accordion className="mb-3">
                                        <Card style={{overflow: 'visible'}}>
                                            <Accordion.Toggle as={Card.Header} eventKey="0"
                                                              className="cursor-pointer small-header">
                                                {translate('ttl_visualization')}
                                            </Accordion.Toggle>
                                            <Accordion.Collapse eventKey="0">
                                                <Card.Body>
                                                    <Form.Group>
                                                        <Form.Check
                                                            type="switch"
                                                            id="show-table"
                                                            label={translate('lbl_show_table')}
                                                            checked={this.state.showTable}
                                                            onChange={this.onVisualizationChanges.bind(this, 'showTable')}
                                                        />
                                                    </Form.Group>
                                                    <Form.Group>
                                                        <Form.Check
                                                            type="switch"
                                                            id="show-chart"
                                                            label={translate('lbl_show_chart')}
                                                            checked={this.state.showChart}
                                                            onChange={this.onVisualizationChanges.bind(this, 'showChart')}
                                                        />
                                                    </Form.Group>

                                                    {this.state.showChart &&
                                                    <Form.Group>
                                                        <Form.Label>{translate('lbl_chart_type')}</Form.Label>
                                                        <Form.Control as="select" value={this.props.chartType} custom
                                                                      onChange={this.onChartTypeSelect}>
                                                            <option value="">{translate('lbl_chart_type_none')}</option>
                                                            <option value="Line">{translate('lbl_chart_type_line')}</option>
                                                            <option value="Bar">{translate('lbl_chart_type_bar')}</option>
                                                            <option value="Area">{translate('lbl_chart_type_area')}</option>
                                                            <option value="Pie">{translate('lbl_chart_type_pie')}</option>
                                                        </Form.Control>
                                                    </Form.Group>
                                                    }

                                                </Card.Body>
                                            </Accordion.Collapse>
                                        </Card>
                                    </Accordion>
                                }

                                <Accordion className="mb-3">
                                    <Card style={{ overflow: 'visible' }}>
                                        <Accordion.Toggle as={Card.Header} eventKey="0" className="cursor-pointer small-header">
                                            {translate('ttl_save')}
                                        </Accordion.Toggle>
                                        <Accordion.Collapse eventKey="0">
                                            <Card.Body>
                                                <Form.Group>
                                                    <Form.Label>{translate('lbl_name')}</Form.Label>
                                                    <Form.Control type="text" className="w-100 mr-2" value={this.state.name} onChange={this.updateName} />
                                                </Form.Group>
                                                { this.state.otherUsers.users && this.state.ownerShip === 'owner' &&
                                                    <Form.Group>
                                                        <Form.Label>{translate('lbl_shared_users')}</Form.Label>
                                                        <Select options={this.state.otherUsers.users}
                                                                placeholder={translate('lbl_do_select')}
                                                                className="filter-select" classNamePrefix="filter-select"
                                                                name="sharedUsers"
                                                                onChange={this.updateSharingOptions.bind(this, 'sharedUsers')}
                                                                value={this.state.sharedUsers}
                                                                isMulti={true}
                                                        />
                                                    </Form.Group>
                                                }
                                                { this.state.otherUsers.roles && this.state.ownerShip === 'owner' &&
                                                    <Form.Group>
                                                        <Form.Label>{translate('lbl_shared_roles')}</Form.Label>
                                                        <Select options={this.state.otherUsers.roles}
                                                                className="filter-select" classNamePrefix="filter-select"
                                                                placeholder={translate('lbl_do_select')}
                                                                name="sharedRoles"
                                                                onChange={this.updateSharingOptions.bind(this, 'sharedRoles')}
                                                                value={this.state.sharedRoles}
                                                                isMulti={true}
                                                        />
                                                    </Form.Group>
                                                }

                                                <div className="d-flex align-items-center">
                                                    <span className="align-middle mr-auto">
                                                        { this.state.ownerShip === 'owner' &&
                                                            <a className="text-danger" href="#" onClick={this.deleteConfiguration} >{translate('btn_delete')}</a>
                                                        }
                                                        { this.state.ownerShip === 'userShared' &&
                                                            <a className="text-danger" href="#" onClick={this.deleteConfiguration} >{translate('btn_unshare')}</a>
                                                        }
                                                    </span>
                                                    <Button className="" variant="primary" disabled={!this.state.changed || this.state.name.length < 1} onClick={this.saveConfiguration} >{this.state.ownerShip === 'owner' ? translate('btn_save_configuration') : translate('btn_save_copy')} </Button>
                                                </div>
                                            </Card.Body>
                                        </Accordion.Collapse>
                                    </Card>
                                </Accordion>

                            </div>
                            }

                        </Card.Body>

                    </Card>

                    <Card className="mt-3">
                        <Card.Header>{translate('ttl_load_existing')}</Card.Header>
                        <Card.Body>
                            <Form.Group>
                                <div className="d-flex flex-row">
                                    <Select
                                        options={this.state.configurationList}
                                        onChange={this.updateConfigurationToLoad}
                                        className="w-100 mr-2"
                                        placeholder={translate('lbl_do_select')}
                                        value={this.state.selectedConfigurationToLoad}
                                    />
                                    <Button variant="outline-primary" onClick={this.loadConfiguration}>{translate('btn_load')}</Button>
                                </div>
                            </Form.Group>

                            <hr/>


                        </Card.Body>
                    </Card>
                </Col>
                <Col xs={12} md={8}>
                    <Card>
                        <Card.Header>{translate('ttl_preview')}</Card.Header>
                        <Card.Body>
                            <ResultContainer
                                configurationId={this.state.configurationId}
                                showTable={this.state.showTable}
                                showChart={this.state.showChart}
                                chartType={this.state.chartType}
                                dataUrl={this.props.resultDataUrl}
                                selectedDataSource={this.state.selectedDataSource}
                                statisticMode={this.state.statisticMode}
                                rows={this.state.rows}
                                columns={this.state.columns}
                                filters={this.state.filters}
                                fieldSettings={this.state.fieldSettings}
                            />

                        </Card.Body>

                    </Card>
                </Col>
            </Row>
        );
    }

}

export default StatisticsConfigContainer;



