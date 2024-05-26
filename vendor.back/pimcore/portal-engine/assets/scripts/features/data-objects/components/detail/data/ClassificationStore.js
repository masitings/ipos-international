/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from "react";
import Tabs from "~portal-engine/scripts/components/tab/Tabs"
import Tab from "~portal-engine/scripts/components/tab/Tab"
import Table from "~portal-engine/scripts/components/Table"
import Trans from "~portal-engine/scripts/components/Trans"
import {getEmptyValue} from "~portal-engine/scripts/features/data-objects/components/detail/BasicValueRow";
import {extractLabel, extractStyle, renderLayout, getLanguageConfig} from "~portal-engine/scripts/features/element/element-layout";
import {getClassificationStoreLayoutDefinition} from "~portal-engine/scripts/features/data-objects/object-layout";

function extractClassificationStoreData(data, name, language = null, defaultValue = null) {
    return data || defaultValue;
}

export function classificationStoreRenderValue(layout, content) {
    let value = getEmptyValue();

    if (content) {
        value = content;
    }

    return (
        <tr>
            <td>{extractLabel(layout)}</td>
            <td className="data-type__value">{value}</td>
        </tr>
    );
}

function ClassificationStoreValue({keyId, value}) {
    const definition = getClassificationStoreLayoutDefinition(keyId);

    if (!definition) {
        return null;
    }

    return renderLayout(definition, value, null, extractClassificationStoreData, classificationStoreRenderValue);
}

export function ClassificationStoreData({config}) {
    if (!config.groups || !config.groups.length) {
        return null;
    }

    const groups = config.groups.filter((group) => group.keys && group.keys.length);

    return (
        <Fragment>
            {groups.map((group, i) => (
                <div className="data-type data-type--classification table-responsive" key={i}>
                    <Table className="table table-hover">
                        <thead>
                        <tr>
                            <th colSpan={2}>
                                <Trans t={group.name} domain="data-object"/>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        {group.keys.map((key, i) => (
                            <ClassificationStoreValue
                                key={i}
                                keyId={key.id}
                                value={key.value}
                            />
                        ))}
                        </tbody>
                    </Table>
                </div>
            ))}
        </Fragment>
    );
}

export default function ({layout, data, language, extractData, renderValue, className = ''}) {
    const extracted = extractData(data, layout.name, language);

    if (!extracted || !extracted.length) {
        return renderValue(layout, null, className);
    }

    const label = extractLabel(layout);

    // only one, do not render tabs
    if (extracted.length === 1) {
        const item = extracted[0];

        return (
            <div className={`data-type data-type--classification-store  ${className}`}>
                {label &&
                <div className="data-type__label">
                    {label}:
                </div>
                }

                <ClassificationStoreData config={item}/>
            </div>
        );
    }

    return (
        <div className={`data-type data-type--classification-store ${className}`} style={extractStyle(layout)}>
            {label &&
            <div className="data-type__label">
                {label}:
            </div>
            }

            <Tabs>
                {extracted.map((item, i) => {
                    const languageConfig = getLanguageConfig(item.language);
                    const languageName = languageConfig ? languageConfig.name : (<Trans t={item.language} domain="data-object"/>);
                    const icon = languageConfig ? languageConfig.icon : null;

                    return (
                        <Tab tab={i} label={languageName} key={i} icon={icon ? (<img src={icon} className="img-fluid"/>) : null}>
                            <ClassificationStoreData config={item}/>
                        </Tab>
                    );
                })}
            </Tabs>
        </div>
    );
}