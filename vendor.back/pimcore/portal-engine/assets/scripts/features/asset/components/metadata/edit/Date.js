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
import DatePicker from "react-datepicker";
import {metadataLabel, updateMetadata} from "~portal-engine/scripts/features/asset/asset-layout";
import FormGroup from "~portal-engine/scripts/components/FormGroup";

export default function ({layout, data, language, extractData, context}) {
    const value = extractData(data, layout.name, language, undefined);
    let date = null;

    if(value) {
        date = new Date(parseInt(value) * 1000);
    }

    return (
        <FormGroup label={metadataLabel(layout, context, language, value)} className={"edit-type edit-type--date vertical-gutter__item"}>
            <DatePicker
                selected={date}
                readOnly={context.readOnly}
                className="form-control"
                onChange={(value) => {
                    updateMetadata(layout, context, language, value.getTime() / 1000);
                }}
            />
        </FormGroup>
    );
}