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
import Accordion from 'react-bootstrap/Accordion';
import Card from 'react-bootstrap/Card';
import Row from 'react-bootstrap/Row';
import Form from 'react-bootstrap/Form';
import Col from 'react-bootstrap/Col';
import Button from 'react-bootstrap/Button';
import Select from 'react-select';
import translate from './TranslationService';

function FilterRow(props) {
    function onUpdateSelect(fieldname, value) {
        const event = {
            target: {
                name: fieldname,
                value: value.value
            }
        }
        props.onUpdate(event);
    }

    const selectedField = props.fields.filter(option => option.value === props.filterValue.field)

    let operators = [];
    if(selectedField && selectedField[0] && props.filterOperators[selectedField[0].typeGroup]) {
        operators = props.filterOperators[selectedField[0].typeGroup];
    }

    const selectedOperator = operators.filter(option => option.value === props.filterValue.operator);
    let needsFilterValue = false;
    if(selectedOperator && selectedOperator[0]) {
        needsFilterValue = selectedOperator[0].needsFilterValue;
    }

    return (
        <fieldset className="border p-3 mb-2 position-relative">
            <legend className="w-auto h6 px-2">{translate('ttl_filter')}</legend>
            <div className="h6 px-1 text-muted delete-button cursor-pointer" onClick={props.onDelete}>x</div>
            <Form.Row>
                <Form.Label column="sm" lg={3}>
                    {translate('lbl_field')}:
                </Form.Label>
                <Col>
                    <Select options={props.fields} isMulti={false}
                            className="filter-select sm" classNamePrefix="filter-select"
                            name={props.index + ".field"} onChange={onUpdateSelect.bind(this, props.index + ".field")}
                            value={selectedField}
                    />
                </Col>
            </Form.Row>
            <Form.Row>
                <Form.Label column="sm" lg={3}>
                    {translate('lbl_operator')}:
                </Form.Label>
                <Col>
                    <Select options={operators} isMulti={false}
                            className="filter-select sm" classNamePrefix="filter-select"
                            name={props.index + ".operator"} onChange={onUpdateSelect.bind(this, props.index + ".operator")}
                            value={selectedOperator}
                    />
                </Col>
            </Form.Row>
            { needsFilterValue &&
                <Form.Row>
                    <Form.Label column="sm" lg={3}>
                        {translate('lbl_filter')}:
                    </Form.Label>
                    <Col>
                        <Form.Control size="sm" type="text" placeholder=""
                                      name={props.index + ".filter"} onChange={props.onUpdate}
                                      value={props.filterValue.filter}
                        />
                    </Col>
                </Form.Row>
            }

        </fieldset>
    );
}


function Filters(props) {
    return (
        <Accordion className="mb-3">
            <Card style={{ overflow: 'visible' }}>
                <Accordion.Toggle as={Card.Header} eventKey="0" className="cursor-pointer small-header">
                    {translate('ttl_filters')}
                </Accordion.Toggle>
                <Accordion.Collapse eventKey="0">
                    <Card.Body>

                        <Form >
                            {
                                props.filters.map(function(item, index) {
                                    return (
                                        <FilterRow
                                            key={index}
                                            filterValue={item}
                                            filterOperators={props.filterOperators}
                                            fields={props.fields}
                                            index={index}
                                            onUpdate={props.onUpdate.bind(this, 'update')}
                                            onDelete={props.onUpdate.bind(this, 'delete', index)}
                                        />
                                    )
                                })
                            }
                        </Form>

                        <Row>
                            <div className="mx-auto">
                                <Button
                                    style={{minWidth: 100}} className="mr-1" size="sm" variant="outline-primary"
                                    onClick={props.onUpdate.bind(this, 'add')}
                                >
                                    +
                                </Button>
                            </div>
                        </Row>

                    </Card.Body>
                </Accordion.Collapse>
            </Card>
        </Accordion>
    );
}

export default Filters;