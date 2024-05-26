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
import Trans from "~portal-engine/scripts/components/Trans";
import {OverlayTrigger, Tooltip} from "react-bootstrap";
import {ReactComponent as RandomIcon} from "~portal-engine/icons/random";

export default function ({toggleKeepMetadata, isKeepingMetadata}) {
    const tooltip = isKeepingMetadata() ? <Trans t="keeping-metadata" domain="asset"/> :
        <Trans t="keep-metadata" domain="asset"/>;
    const content = (
        <a
            href="#"
            className={`ml-2 d-inline-block ${isKeepingMetadata() ? "icon-light" : ""}`}
            onClick={(event) => {
                event.preventDefault();
                event.stopPropagation();
                toggleKeepMetadata();
            }}
        >
            <RandomIcon height={13}/>
        </a>
    );

    return (
        <OverlayTrigger placement="right" overlay={<Tooltip>{tooltip}</Tooltip>}>
            <span className={"edit-type edit-type--clear"}>
                {content}
            </span>
        </OverlayTrigger>
    );
}