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
import FormGroup from "~portal-engine/scripts/components/FormGroup";
import Select from "react-select";
import {updateMetadata, metadataLabel} from "~portal-engine/scripts/features/asset/asset-layout";
import {transformSelectOptions} from "~portal-engine/scripts/features/element/element-layout";

export default function ({layout, data, language, extractData, context}) {
    const value = extractData(data, layout.name, language, undefined);
    const options = transformSelectOptions(layout.options);

    return (
        <FormGroup label={metadataLabel(layout, context, language, value)} className={"edit-type edit-type-select vertical-gutter__item"}>
            <Select
                className="react-select"
                classNamePrefix={`react-select`}
                value={value}
                isDisabled={context.readOnly}
                options={options}
                onChange={(v) => {
                    if(context.readOnly) {
                        return;
                    }

                    updateMetadata(layout, context, language, v)
                }}
            />
        </FormGroup>
    );
}