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
import FormGroup from "~portal-engine/scripts/components/FormGroup";

function Input({label, value, attribute, config, onChange}) {
    let {defaultValue, ...inputProps} = config;
    value = value ? value : defaultValue;

    return (
        <FormGroup label={label}>
            <input
                {...inputProps}
                value={value ? value : ""}
                className="form-control form-control-sm"
                onChange={(event) => {
                    let value = event.target.value;

                    if(config.min && value && value < config.min) {
                        return;
                    }

                    if(config.max && value && value > config.max) {
                        return;
                    }

                    onChange(attribute, value)
                }}
            />
        </FormGroup>
    );
}

export default Input;