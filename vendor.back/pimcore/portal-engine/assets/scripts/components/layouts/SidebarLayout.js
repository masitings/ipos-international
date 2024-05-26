/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useEffect, useState} from 'react';
import {ReactComponent as ChevronLeft} from "~portal-engine/icons/chevron-left";

export default function ({children, sidebarChildren, className = ''}) {
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);

    useEffect(function () {
        let value = localStorage.getItem('sidebar-open');
        if (value) {
            try {
                let isOpen = JSON.parse(value);
                setIsSidebarOpen(isOpen);
            } catch (e) {}
        }
    }, []);

    useEffect(function () {
        localStorage.setItem('sidebar-open', JSON.stringify(isSidebarOpen));
    }, [isSidebarOpen]);

    return (
        <div className={`sidebar-layout full-height-layout ${isSidebarOpen ? 'is-open' : ''} ${className}`}>
            <div className="container full-height-layout">
                <div className="row no-gutters flex-nowrap full-height-layout__fill">
                    <div className="position-static col-auto d-none d-md-block">
                        <div className="sidebar-layout__side">
                            <div className="sidebar-layout__side__content-wrapper">
                                <div className="row row-gutter--0 h-100 flex-nowrap">
                                    {isSidebarOpen ? (
                                        // todo w-25 is needed for flexbox
                                        <div className="col h-100 w-25">
                                            <div className="sidebar-layout__side__content h-100">
                                                {sidebarChildren}
                                            </div>
                                        </div>
                                    ) : null}

                                    <div className="col-auto h-100">
                                        <button className="sidebar-layout__side-toggle"
                                                onClick={() => setIsSidebarOpen(!isSidebarOpen)}>
                                            <ChevronLeft className={'sidebar-layout__side-toggle-icon'} width="8"/>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div className="col">
                        <div className="sidebar-layout__content main-content__main">
                            {children}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}