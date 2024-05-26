/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState} from "react";
import Trans from "~portal-engine/scripts/components/Trans";
import {Tooltip, OverlayTrigger} from "react-bootstrap";
import {ReactComponent as BanIcon} from "~portal-engine/icons/ban";

export default function ({value, reset, clear}) {
    const [originalValue, setOriginalValue] = useState(value === null ? undefined : value);
    let tooltip, content;

    if (value === null) {
        tooltip = (<Trans t="will-be-cleared" domain="asset"/>);
        content = (
            <a href="#" className="ml-2 icon-light d-inline-block" onClick={(event) => {event.stopPropagation(); event.preventDefault(); reset(originalValue)}}>
                <BanIcon height={13}/>
            </a>
        );
    } else {
        tooltip = (<Trans t="clear" domain="asset"/>)
        content = (
            <a href="#" className="ml-2 d-inline-block" onClick={(event) => {event.stopPropagation(); event.preventDefault(); setOriginalValue(value); clear();}}>
                <BanIcon height={13}/>
            </a>
        );
    }

    return (
        <OverlayTrigger placement="right" overlay={<Tooltip>{tooltip}</Tooltip>}>
            <span className={"edit-type edit-type--clear"}>
                {content}
            </span>
        </OverlayTrigger>
    );
}