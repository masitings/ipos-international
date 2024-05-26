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

export default function ({layout, data, language, extractData, renderValue, className = ''}) {
    const extracted = extractData(data, layout.name, language);

    if (!extracted || !extracted.length) {
        return renderValue(layout, null, className);
    }

    let maxColumnCount = 0;
    extracted.forEach((row) => {
        maxColumnCount = Math.max(maxColumnCount, row.length);
    });

    return (
        <div className={`${className} data-type data-type--table table-responsive`}>
            <Table className={`table table-hover`} style={extractStyle(layout)}>
                <thead>
                <tr>
                    <th colSpan={maxColumnCount}>
                        {extractLabel(layout)}
                    </th>
                </tr>
                </thead>
                <tbody>
                {extracted.map((row, i) => (
                    <tr key={i}>
                        {row.map((column, i) => (
                            <td key={i}>
                                {column}
                            </td>
                        ))}
                    </tr>
                ))}
                </tbody>
            </Table>
        </div>
    );
}