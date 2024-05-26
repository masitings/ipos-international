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
import ReactSelect from "react-select";
import FormGroup from "~portal-engine/scripts/components/FormGroup";

function Select({label, value, attribute, config, onChange}) {
    value = value ? value : config.defaultValue;

    return (
        <FormGroup label={label}>
            <ReactSelect className="react-select"
                         classNamePrefix={`react-select react-select--sm`}
                         options={config.options}
                         value={config.options.find(o => o.value === value)}
                         onChange={(v) => {
                onChange(attribute, v.value);
            }}/>
        </FormGroup>
    );
}

export default Select;