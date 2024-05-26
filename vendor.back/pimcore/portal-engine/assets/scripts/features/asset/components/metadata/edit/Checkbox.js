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
    const id = `metadata_edit_checkbox_${language}_${layout.name}`;

    return (
        <FormGroup label={metadataLabel(layout, context, language, value)} inlineLabel={true} htmlFor={id} className={"edit-type edit-type--checkbox vertical-gutter__item"}>
            <input
                id={id}
                type="checkbox"
                readOnly={context.readOnly}
                disabled={context.readOnly}
                value={value || 0}
                checked={value || 0}
                onChange={(event) => {
                    updateMetadata(layout, context, language, event.target.checked ? 1 : 0)
                }}
            />
        </FormGroup>
    );
}