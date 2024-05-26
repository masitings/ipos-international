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

export default function ({className = "", labelClassName = "", inlineLabel = false, htmlFor = 0, label, info, children}) {
    return (
        <div className={`form-group form-group--custom ${className}`}>
            {label ? (
                <label className={`form-control-label ${!inlineLabel ? "d-block" : "mr-2"} ${labelClassName}`} htmlFor={htmlFor}>
                    {label}
                </label>
            ) : null}

            {children}

            {info ? (
                <small className="form-text text-muted">{info}</small>
            ) : null}
        </div>
    );
}