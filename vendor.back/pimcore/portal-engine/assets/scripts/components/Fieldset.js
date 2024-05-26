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

export default function ({className, titleClassName, bodyClassName, style, title, children}) {
    return (
        <div className={`fieldset ${className}`} style={style}>
            {title && (
                <div className={`fieldset__title ${titleClassName}`}>
                    {title}
                </div>
            )}

            <div className={`${bodyClassName}`}>
                {children}
            </div>
        </div>
    );
}