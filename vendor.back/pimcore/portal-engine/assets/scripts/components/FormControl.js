/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from 'react';

export default function (props) {
    const {
        className = "",
        labelClassName = "",
        type,
        id,
        label,
        value,
        placeholder,
        info,
        inputProps = null,
        onChange,
        min = null,
        max = null,
        readOnly = false
    } = props;

    return (
        <Fragment>
            <label className={`d-block ${labelClassName}`}>
                {label ? (
                    <label className="form-control-label">{label}</label>
                ) : null}

                <input type={type}
                       className={`form-control ${className}`}
                       id={id}
                       readOnly={readOnly}
                       value={value ? value : ""}
                       placeholder={placeholder} {...inputProps}
                       {...(!readOnly && onChange ? {onChange: (evt) => onChange(evt.target.value)}: null)}
                        min={min}
                       max={max}
                />
            </label>

            {info ? (
                <small id="emailHelp" className="form-text text-muted">{info}</small>
            ) : null}
        </Fragment>
    );
}