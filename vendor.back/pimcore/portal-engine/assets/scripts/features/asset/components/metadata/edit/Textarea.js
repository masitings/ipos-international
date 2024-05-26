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
import {metadataLabel, updateMetadata} from "~portal-engine/scripts/features/asset/asset-layout";

export default function ({layout, data, language, extractData, context}) {
    const value = extractData(data, layout.name, language, undefined);

    return (
        <FormGroup label={metadataLabel(layout, context, language, value)} className={"edit-type edit-type--textarea vertical-gutter__item"}>
            <textarea
                className="form-control"
                value={value || ""}
                readOnly={context.readOnly}
                onChange={!context.readOnly ? (e) => {
                    updateMetadata(layout, context, language, e.target.value)
                } : null}
            />
        </FormGroup>
    );
}