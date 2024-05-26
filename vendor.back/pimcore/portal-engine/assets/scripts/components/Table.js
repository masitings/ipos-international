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

export default function ({children, striped, className, style}) {
    return (
        <div className="table-responsive" style={style}>
            <table className={`table wysiwyg ${striped ? "table-striped" : ""} ${className}`}>
                {children}
            </table>
        </div>
    );
}