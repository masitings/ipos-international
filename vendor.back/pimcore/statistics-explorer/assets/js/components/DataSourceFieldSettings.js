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
import Accordion from 'react-bootstrap/Accordion';
import Card from 'react-bootstrap/Card';
import Form from 'react-bootstrap/Form';
import Col from 'react-bootstrap/Col';
import { toast } from 'react-toastify';
import handleErrors from './fetchErrorHandler';
import translate from "./TranslationService";

function InputField(props) {

    return (
        <Form.Row>
            <Form.Label column="sm" lg={3}>
                {props.label}
            </Form.Label>
            <Col>
                <Form.Control
                    name={props.name}
                    size="sm" type="input"
                    onChange={props.onChange}
                    value={( props.value )}
                />
            </Col>
        </Form.Row>
    );

}

function NumberField(props) {

    return (
        <Form.Row>
            <Form.Label column="sm" lg={3}>
                {props.label}
            </Form.Label>
            <Col>
                <Form.Control
                    name={props.name}
                    size="sm" type="number"
                    onChange={props.onChange}
                    value={( props.value )}
                />
            </Col>
        </Form.Row>
    );

}

function CheckboxField(props) {

    return (
        <Form.Row>
            <Form.Label column="sm" lg={3}>
                {props.label}
            </Form.Label>
            <Col>
                <Form.Check
                    type="checkbox"
                    name={props.name}
                    size="sm"
                    onChange={props.onChange}
                    checked={( props.value )}
                />
            </Col>
        </Form.Row>
    );

}

function SelectField(props) {

    return (
        <Form.Row>
            <Form.Label column="sm" lg={3}>
                {props.label}
            </Form.Label>
            <Col>
                <Form.Control name={props.name} as="select" value={props.value} size="sm" custom onChange={props.onChange}>
                    {
                        props.options.map((item, index) =>
                            <option key={index} value={item.value}>{item.label}</option>
                        )
                    }
                </Form.Control>
            </Col>
        </Form.Row>
    );

}


class FieldSetting extends React.Component {

    constructor (props) {
        super(props);
        this.onSettingUpdate = this.onSettingUpdate.bind(this);
    }

    getValues() {
        let values = {
            typeGroup: this.props.typeGroup
        };
        this.props.item.fields.forEach(function(field) {
            if(this.props.fieldSettings[field.name] === undefined) {
                values[field.name] = field.defaultValue;
            } else {
                values[field.name] = this.props.fieldSettings[field.name];
            }
        }.bind(this));
        return values;
    }

    componentDidMount() {
        this.props.handleChange(this.props.item.name, this.getValues());
    }

    onSettingUpdate(event) {
        const name = event.target.name;
        const value = event.target.type === 'checkbox' ? event.target.checked : event.target.value;

        let values = this.getValues();
        values[name] = value;
        this.props.handleChange(this.props.item.name, values);
    }

    componentWillUnmount() {
        this.props.handleChange(this.props.item.name, null);
    }

    render () {
        let values = {};
        let fields = [];
        this.props.item.fields.forEach(function(field) {
            let value = this.props.fieldSettings[field.name];
            if(value === undefined) {
                value = field.defaultValue;
            }
            values[field.name] = value;
        }.bind(this));

        this.props.item.fields.forEach(function(field) {

            if(field.visibleCheckField && values[field.visibleCheckField] !== field.visibleCheckValue) {
                return;
            }

            const value = values[field.name];

            switch (field.type) {
                case 'input':
                    fields.push(
                        <InputField
                            key={this.props.item.name + '_' + field.name}
                            label={field.label}
                            name={field.name}
                            value={value}
                            onChange={this.onSettingUpdate}
                        />
                    );
                    break;
                case 'number':
                    fields.push(
                        <NumberField
                            key={this.props.item.name + '_' + field.name}
                            label={field.label}
                            name={field.name}
                            value={value}
                            onChange={this.onSettingUpdate}
                        />
                    );
                    break;
                case 'checkbox':
                    fields.push(
                        <CheckboxField
                            key={this.props.item.name + '_' + field.name}
                            label={field.label}
                            name={field.name}
                            value={value}
                            onChange={this.onSettingUpdate}
                        />
                    );
                    break;
                case 'select':
                    fields.push(
                        <SelectField
                            key={this.props.item.name + '_' + field.name}
                            label={field.label}
                            name={field.name}
                            options={field.options}
                            value={value}
                            onChange={this.onSettingUpdate}
                        />
                    );
                    break;
            }
        }.bind(this));

        return (
            <fieldset className={`border p-3 position-relative ${this.props.className}`}>
                <legend className="w-auto h6 px-2">{this.props.item.label}</legend>
                {fields}
            </fieldset>
        );
    }
}


export default function DataSourceFieldSettings(props) {
    const [fieldSettingsConfig, setFieldSettingsConfig] = useState([]);
    const requestParams = new URLSearchParams({
        dataSource: props.selectedDataSource.value,
        statisticMode: props.statisticMode,
        rows: JSON.stringify(props.rows),
        columns: JSON.stringify(props.columns)
    });

    const onUpdateSettings = props.onUpdateSettings;
    const fieldSettings = props.fieldSettings;

    useEffect(() => {
        fetch(props.loadFieldSettingsUrl + "?" + requestParams)
            .then(handleErrors)
            .then(res => res.json())
            .then(
                (result) => {
                    setFieldSettingsConfig(result.fieldSettings);
                }
            )
            .catch(error => {
                console.error(error);
                toast.error('Error loading field setting definitions.', {autoClose: false});
            })
        ;
    }, [requestParams.toString()]);

    if(props.statisticMode === 'statistic' && (!props.rows || props.rows.length < 1) || props.statisticMode === 'list' && (!props.columns || props.columns.length < 1)) {
        return null;
    }
    return (
        <Accordion className="mb-3">
            <Card style={{ overflow: 'visible' }}>
                <Accordion.Toggle as={Card.Header} eventKey="0" className="cursor-pointer small-header">
                    {translate('ttl_field_settings')}
                </Accordion.Toggle>
                <Accordion.Collapse eventKey="0">
                    <Card.Body>
                        <div className="field-settings">
                            {
                                fieldSettingsConfig.map((item) =>
                                    <FieldSetting
                                        key={item.name}
                                        item={item}
                                        fieldSettings={fieldSettings[item.name] || {}}
                                        typeGroup={item.typeGroup}
                                        handleChange={onUpdateSettings}
                                        className="item"
                                    />
                                )
                            }
                        </div>
                    </Card.Body>
                </Accordion.Collapse>
            </Card>
        </Accordion>
    );
}