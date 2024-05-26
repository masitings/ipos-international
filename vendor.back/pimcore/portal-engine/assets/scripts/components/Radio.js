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

export default ({label, id, name, checked = false, onChange = noop, className = '', children}) => (
    <div className={`custom-radio form-check ${className}`} key={id}>
        <label>
            <input type="radio"
                   className="custom-radio__input form-check-input"
                   checked={checked}
                   onChange={onChange}
                   id={id}
                   name={name}/>
            <span className="custom-radio__box"/>
            <span className="custom-radio__text form-check-label">{children || label}</span>
        </label>
    </div>
);