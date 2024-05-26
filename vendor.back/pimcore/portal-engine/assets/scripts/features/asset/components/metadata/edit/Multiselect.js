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
import {transformSelectOptions} from "~portal-engine/scripts/features/element/element-layout";
import Select from "react-select";
import FormGroup from "~portal-engine/scripts/components/FormGroup";
import {metadataLabel, updateMetadata} from "~portal-engine/scripts/features/asset/asset-layout";

export default function ({layout, data, language, extractData, context}) {
    const value = extractData(data, layout.name, language, undefined);

    return (
        <FormGroup label={metadataLabel(layout, context, language, value)} className={"edit-type edit-type--multi-select vertical-gutter__item"}>
            <Select
                className="react-select"
                classNamePrefix={`react-select`}
                value={value}
                options={transformSelectOptions(layout.options)}
                readOnly={context.readOnly}
                isDisabled={context.readOnly}
                isMulti={true}
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