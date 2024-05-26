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
import {noop} from "~portal-engine/scripts/utils/utils";
import {ReactComponent as AngleIcon} from "~portal-engine/icons/angle-down";

export default ({label, id, value, name, onChange = noop, className = '', readOnly = false, options = []}) => (
    <div className={`select form-group ${className}`} key={id}>
        <label htmlFor={id} className="form-control-label">{label}</label>

        <div className='position-relative'>
            <select className="form-control" value={value} id={id} onChange={!readOnly ? (e) => onChange(e.target.value) : null} name={name}>
                {options.map((item, index) => (
                    <option key={index} value={item.value}>
                        {item.text ? item.text : item.key}
                    </option>
                ))}
            </select>
            <AngleIcon className="select__icon" height="15"/>
        </div>
    </div>
);