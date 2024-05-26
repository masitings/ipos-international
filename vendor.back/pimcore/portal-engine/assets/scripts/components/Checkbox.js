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
import {ReactComponent as CheckIcon} from "~portal-engine/icons/check";
import DOMPurify from 'dompurify';

export default ({
    label,
    id,
    name,
    checked = false,
    className = '',
    readOnly = false,
    disabled = false,
    style = {},
    allowHtml = false,
    onChange = noop
}) => (
    <div className={`custom-checkbox form-check ${className}`} style={style} key={id}>
        <label htmlFor={id}>
            <input type="checkbox"
                   className="custom-checkbox__input form-check-input"
                   readOnly={readOnly}
                   disabled={disabled}
                   checked={checked}
                   onChange={(evt) => onChange(evt.target.value)}
                   id={id}
                   name={name}/>
            <span className="custom-checkbox__box">
                <CheckIcon className="custom-checkbox__box__icon "/>
            </span>
            <span className="custom-checkbox__text form-check-label">
                {allowHtml ? (
                    <span dangerouslySetInnerHTML={{__html: DOMPurify.sanitize(label)}}/>
                ): label}
            </span>
        </label>
    </div>
);