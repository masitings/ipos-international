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
import {extractStyle, extractLabel} from "~portal-engine/scripts/features/element/element-layout";
import Table from "~portal-engine/scripts/components/Table";
import Trans from "~portal-engine/scripts/components/Trans";

export default function ({layout, data, language, extractData, renderValue, className = ''}) {
    const extracted = extractData(data, layout.name, language);

    if (!extracted || !extracted.length) {
        return renderValue(layout, null, className);
    }

    const firstRow = extracted[0];
    const columns = [];

    for (let [key] of Object.entries(firstRow)) {
        if (!key.startsWith("__")) {
            columns.push(key);
        }
    }

    return (
        <div className={`data-type data-type--structured table-responsive ${className}`}>
            <Table className="table table-hover" style={extractStyle(layout)}>
                <thead>
                <tr>
                    <th>{extractLabel(layout)}</th>
                    {columns.map((column, i) => (
                        <th key={i}><Trans t={column} domain="data-object"/></th>
                    ))}
                </tr>
                </thead>
                <tbody>
                {extracted.map((row, i) => (
                    <tr key={i}>
                        <td><Trans t={row.__row_identifyer} domain="data-object"/></td>

                        {columns.map((column, i) => (
                            <td key={i}>{row[column]}</td>
                        ))}
                    </tr>
                ))}
                </tbody>
            </Table>
        </div>

    );
}