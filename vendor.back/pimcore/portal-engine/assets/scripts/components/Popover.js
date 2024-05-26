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
import {OverlayTrigger, Popover} from "react-bootstrap";

export default function ({className, content, children, placement = "auto"}) {
    return (
        <OverlayTrigger
            placement={placement}
            overlay={
                <Popover id={`popover-positioned-${placement}`} className={ className }>
                    <Popover.Content>
                        { content }
                    </Popover.Content>
                </Popover>
            }
        >

            { children }
        </OverlayTrigger>
    );
}