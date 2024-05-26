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

export default function ({className = '', progress}) {
    return (
        <div className={`progress ${className}`}>
            <div className="progress-bar" role="progressbar" style={{width: progress + "%"}}></div>
        </div>
    );
}