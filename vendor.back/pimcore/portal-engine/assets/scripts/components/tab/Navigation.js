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
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";

export default function ({setCurrentTab, tabs, currentTab, classNames}) {
    return (
        <ul className={`nav nav-tabs ${classNames.wrapper}`}>
            {tabs.map((tab, i) => (
                <li className={`nav-item ${classNames.item}`} key={i}>
                    <button type="button" className={`nav-link ${tab.props.tab === currentTab.props.tab ? "active" : ""}`} onClick={() => setCurrentTab(tab)}>
                        {tab.props.icon ? (
                            <span className="nav-link__icon">{tab.props.icon}</span>
                        ) : null}

                        {tab.props.label}
                    </button>

                    {tab.props.onClose && tab.props.tab === currentTab.props.tab ? (
                        <button type="button" className="nav-item__close btn btn-link btn-sm px-1" onClick={() => tab.props.onClose(tab.props.tab)}>
                            <CloseIcon height={10}/>
                        </button>
                    ) : null}
                </li>
            ))}
        </ul>
    );
}