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
import DataTableRow from "~portal-engine/scripts/components/DataTableRow";


export default function DataTable (props) {
    let {
        className = "",
        items,
        listViewAttributes,
        RowComponent = DataTableRow
    } = props;

    return (
        <div className={`data-table-container ${className}`}>
            <div className="data-table table-responsive">
                <table className="table">
                    <thead>
                    <tr>
                        <th colSpan="2"/>
                        {listViewAttributes.map((attribute, index) => (
                            <th key={index}>{attribute.label}</th>
                        ))}
                    </tr>
                    </thead>
                    <tbody>
                    {items.map((item, index) => (
                        <RowComponent key={index} id={item.id} listViewAttributes={listViewAttributes}/>
                    ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}