/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from 'react';
import {ReactComponent as EllipsisIcon} from "~portal-engine/icons/ellipsis-v";
import {Dropdown} from "react-bootstrap";
import Trans from "~portal-engine/scripts/components/Trans";
import {defaultActionConfig} from "~portal-engine/scripts/components/actions/ActionBar";

export default function (props) {
    const {
        classNames = {
            toggle: '',
            wrapper: ''
        },
        actions = defaultActionConfig,
        actionHandler = {},
        actionUrls = {},
    } = props;

    return (
        <Dropdown className={`dropdown dropdown--sm ${classNames.wrapper}`}>
            <Dropdown.Toggle variant="link" className={classNames.toggle}>
                <EllipsisIcon height="20"/>
            </Dropdown.Toggle>

            {/*renderOnMount is needed otherwise first open position is wrong */}
            <Dropdown.Menu popperConfig={{strategy : "fixed"}} alignRight="true" renderOnMount>
                {actions
                    .filter(({handlerName}) =>
                        (actionHandler[handlerName] && typeof actionHandler[handlerName] === "function")
                        || (actionUrls[handlerName] && typeof actionUrls[handlerName] === "string"
                        ))
                    .map(({id, handlerName, translationKey}) =>
                        actionUrls[handlerName]
                            ? (
                                <Dropdown.Item key={id} href={actionUrls[handlerName]}>
                                    <Trans t={translationKey} domain="action-bar"/>
                                </Dropdown.Item>
                            ) : (
                                <Dropdown.Item key={id} onClick={actionHandler[handlerName]}>
                                    <Trans t={translationKey} domain="action-bar"/>
                                </Dropdown.Item>
                            ))}

            </Dropdown.Menu>
        </Dropdown>
    );
}