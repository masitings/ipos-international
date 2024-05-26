/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from "react";
import Navigation from "~portal-engine/scripts/components/tab/Navigation";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import Media from "react-media";

function Mobile({nav, addition}) {
    return (
        <Fragment>
            {addition !== null && (
                <div className="d-flex justify-content-end mb-2">
                    {addition}
                </div>
            )}

            <div className={`nav nav-tabs overflow-visble`}>
                {nav}
            </div>
        </Fragment>
    );
}

function Desktop({nav, addition}) {
    return (
        <div className={`nav nav-tabs d-flex justify-content-between align-items-center overflow-visible`}>
            {nav}

            {addition !== null && (
                <div className="ml-3 flex-shrink-1">
                    {addition}
                </div>
            )}
        </div>
    )
}

export default function ({setCurrentTab, tabs, currentTab, classNames, addition = null}) {
    const nav = (
        <Navigation classNames={classNames} setCurrentTab={setCurrentTab} tabs={tabs} currentTab={currentTab}/>
    );

    return (
        <Media queries={{small: MD_DOWN}}>
            {matches => (
                matches.small
                ? <Mobile nav={nav} addition={addition}/>
                : <Desktop nav={nav} addition={addition}/>
            )}
        </Media>
    );
}