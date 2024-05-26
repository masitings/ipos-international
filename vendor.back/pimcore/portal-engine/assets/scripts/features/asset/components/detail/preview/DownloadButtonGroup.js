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

function DownloadButtonGroup({children}) {
    return (
        <div className="d-flex justify-content-center align-items-center flex-wrap vertical-gutter vertical-gutter--3">
            {children}
        </div>
    );
}

export default DownloadButtonGroup;