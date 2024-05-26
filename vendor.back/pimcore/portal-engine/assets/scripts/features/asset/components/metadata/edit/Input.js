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
import FormGroup from "~portal-engine/scripts/components/FormGroup";
import FormControl from "~portal-engine/scripts/components/FormControl";
import {metadataLabel, updateMetadata} from "~portal-engine/scripts/features/asset/asset-layout";

export default function ({layout, data, language, extractData, context}) {
    const value = extractData(data, layout.name, language, undefined);

    return (
        <FormGroup label={metadataLabel(layout, context, language, value)} value={value} className={"edit-type edit-type--input vertical-gutter__item"}>
            <FormControl
                type="text"
                readOnly={context.readOnly}
                value={value}
                onChange={(value) => {
                    updateMetadata(layout, context, language, value)
                }}
            />
        </FormGroup>
    );
}