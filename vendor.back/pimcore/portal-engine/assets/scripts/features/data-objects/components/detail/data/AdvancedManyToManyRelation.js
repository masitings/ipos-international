/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from "react";
import {extractLabel} from "~portal-engine/scripts/features/element/element-layout";
import Table from "~portal-engine/scripts/components/Table";
import Trans from "~portal-engine/scripts/components/Trans";

export default function ({layout, data, language, extractData, renderValue, className = ''}) {
    let extracted = extractData(data, layout.name, language);

    if (!extracted || !Array.isArray(extracted.data) || !extracted.data.length) {
        return renderValue(layout, null, className);
    }

    let label = extractLabel(layout);

    return (
        <div className={`${className} data-type data-type--advanced-many-to-many table-responsive`}>
            <Table className="table table-hover">
                <thead>
                <tr>
                    <th>{label ? label : <Trans t="name" domain="data-object"/>}</th>
                    {extracted.meta.map((column, i) => {
                        return (
                            <th key={i}><Trans t={column} domain="data-object"/></th>
                        );
                    })}
                </tr>
                </thead>
                <tbody>
                {extracted.data.map((item, i) => {
                    let content = item.name || item.path;

                    if (item.url) {
                        content = (<a href={item.url} className="text-primary">{content}</a>);
                    }

                    return (
                        <tr key={i}>
                            <td>{content}</td>
                            {extracted.meta.map((column, i) => {
                                return (
                                    <td key={i}>{item[column]}</td>
                                );
                            })}
                        </tr>
                    );
                })}
                </tbody>
            </Table>
        </div>

    );
}