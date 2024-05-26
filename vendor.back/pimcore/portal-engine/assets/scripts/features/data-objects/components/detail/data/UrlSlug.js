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

export default function ({layout, data, language, extractData}) {
    const extracted = extractData(data, layout.name, language);
    let content = null;

    if(extracted && Array.isArray(extracted)) {
        content = (
            <div className="data-type data-type--url-slug table-responsive vertical-gutter__item">
                <Table className="table table-hover">
                    <thead>
                    <tr>
                        <th>{extractLabel(layout)}</th>
                        <th><Trans t="slug" domain="data-object"/></th>
                    </tr>
                    </thead>
                    <tbody>
                    {extracted.map((item, i) => (
                        <tr key={i}>
                            <td>{item.domain ? item.domain : (<Trans t="slug-fallback" domain="data-object"/>)}</td>
                            <td className="data-type__value">{item.slug}</td>
                        </tr>
                    ))}
                    </tbody>
                </Table>
            </div>
        );
    }

    return content;
}