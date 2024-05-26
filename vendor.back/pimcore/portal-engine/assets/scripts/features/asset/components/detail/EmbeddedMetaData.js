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
import Table from "~portal-engine/scripts/components/Table";
import Trans from "~portal-engine/scripts/components/Trans";

function EmbeddedMetaData({data}) {
    return (
        <Table>
            <thead>
                <tr>
                    <th><Trans t="embedded-metadata-name" domain="asset"/></th>
                    <th><Trans t="embedded-metadata-value" domain="asset"/></th>
                </tr>
            </thead>

            <tbody>
            {Object.entries(data).map(([key, value]) => (
                <tr key={key}>
                    <td className="text-nowrap pr-4 pr-md-5"><Trans t={key} domain="asset"/></td>
                    <td width="99%">{value}</td>
                </tr>
            ))}
            </tbody>
        </Table>
    );
}

export default EmbeddedMetaData;