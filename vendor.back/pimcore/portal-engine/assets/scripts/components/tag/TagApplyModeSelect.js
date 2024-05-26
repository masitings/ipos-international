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
import Select from "react-select";
import * as ApplyModes from "~portal-engine/scripts/consts/tags-apply-modes";
import Trans from "~portal-engine/scripts/components/Trans";

function TagApplyModeSelect(props) {
    const {
        value = null,
        onChange = () => {
        }
    } = props;

    const options = Object.entries(ApplyModes).map(([key, value]) => {
        return {
            label: (<Trans t={value}/>),
            value: value
        }
    });
    const selected = options.find((option) => option.value === value);

    return (
        <Select
            className="react-select"
            classNamePrefix={`react-select`}
            options={options}
            value={selected}
            onChange={(value) => {
                onChange(value.value);
            }}
        />
    );
}

export default TagApplyModeSelect;