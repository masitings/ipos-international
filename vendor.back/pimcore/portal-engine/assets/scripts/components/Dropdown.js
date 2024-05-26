/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from 'react';
import {Dropdown} from "react-bootstrap";
import {ReactComponent as ChevronDown} from "~portal-engine/icons/chevron-down";
import {noop} from "~portal-engine/scripts/utils/utils";

export default function (props = {}) {
    const {
        items = [],
        activeItem,
        onDropdownClickItem = noop,
        classNames = {
            toggle: '',
            wrapper: ''
        },
        toggleIconComponent
    } = props;

    return (
        <Fragment>
            {items.length ? (
                <Dropdown className={classNames.wrapper}>
                    <Dropdown.Toggle variant={props.variant} className={classNames.toggle}>
                        {toggleIconComponent}
                        {props.title}

                        {activeItem ? (
                           <span className="ml-1">{items.find((item) => activeItem === item.value).label}</span>
                        ): null}
                        <ChevronDown width="12" height="12" className="ml-2" />
                    </Dropdown.Toggle>

                    <Dropdown.Menu>
                        {items.map((item, i) => (
                            <Dropdown.Item className={`${activeItem === item.value ? 'active' : ''}`} key={i} href="#" onClick={() =>  {onDropdownClickItem(item);}}>{item.label}</Dropdown.Item>
                        ))}
                    </Dropdown.Menu>
                </Dropdown>
            ) : null }
        </Fragment>
    );
}