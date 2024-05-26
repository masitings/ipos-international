/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState} from 'react';
import {Collapse} from 'react-collapse';
import {ReactComponent as AngleIcon} from "~portal-engine/icons/angle-down";

export default function (props) {
    const {
        className = "",
        bodyClassName = "",
        title,
        headerAddition = null,
        children,
        collapsible = true,
        collapsed = false,
        style = {},
    } = props;

    const [isOpen, setIsOpen] = useState(!collapsed || !collapsible);

    let header = (
        <span className="card-header__btn d-block">
            {title}
        </span>
    );

    if(collapsible) {
        header = (
            <button
                className="card-header__btn d-block"
                type="button"
                data-toggle="collapse" aria-expanded={isOpen ? 'true': 'false'}
                onClick={() => setIsOpen(!isOpen)}
            >
                <AngleIcon width="10" className="card-header__btn__icon" />

                { title }
            </button>
        );
    }

    return (
        <div className={`card ${className} ${isOpen ? 'is-open' : null}`} style={style}>
            <div className="card-header d-flex justify-content-between align-items-center">
                <h3 className="mb-0">
                    {header}
                </h3>

                {headerAddition}
            </div>

            <Collapse theme={{collapse: 'collapse-container', content: `card-body ${bodyClassName}`}} isOpened={isOpen}>
                { children }
            </Collapse>
        </div>
    );
}