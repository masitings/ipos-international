/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {forwardRef} from "react";

const ClearDropdownToggle = forwardRef(({children, onClick, ...props}, ref) => {
    return (
        <a
            href=""
            ref={ref}
            onClick={(event) => {
                event.preventDefault();
                onClick(event);
            }}
            {...props}
        >
            {children}
        </a>
    );
});

export default ClearDropdownToggle;