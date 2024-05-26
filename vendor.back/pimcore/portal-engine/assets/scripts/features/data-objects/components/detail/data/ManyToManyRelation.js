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
import {extractLabel, extractStyle} from "~portal-engine/scripts/features/element/element-layout";
import Table from "~portal-engine/scripts/components/Table";

export default function ({layout, data, language, extractData, renderValue, className = ''}) {
    let extracted = extractData(data, layout.name, language);

    if (!extracted || !Array.isArray(extracted) || !extracted.length) {
        return renderValue(layout, null, className);
    }

    let label = extractLabel(layout);

    return (
        <div className={`${className} data-type data-type--many-to-many table-responsive`} style={extractStyle(layout)}>
            <Table className="table table-hover">
                {label &&
                <thead>
                <tr>
                    <th>{label}</th>
                </tr>
                </thead>
                }

                <tbody>
                {extracted.map((item, i) => {
                    let content = item.name || item.path;

                    if (item.url) {
                        content = (<a href={item.url} className="text-primary">{content}</a>);
                    }

                    return (
                        <tr key={i}>
                            <td>{content}</td>
                        </tr>
                    );
                })}
                </tbody>
            </Table>
        </div>

    );
}