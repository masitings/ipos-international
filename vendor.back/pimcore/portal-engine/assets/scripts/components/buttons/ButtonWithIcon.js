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
import Button from "react-bootstrap/Button";

export default ({Icon, children, isPulsing = false, className = "", ...props}) => (
    <Button type="button" className={`btn-rounded btn-with-addon ${className}`} {...props}>
        <span className={`btn__addon ${isPulsing ? 'btn__addon--pulsing': ''}`}>
            {Icon}
        </span>

        {children}
    </Button>
)