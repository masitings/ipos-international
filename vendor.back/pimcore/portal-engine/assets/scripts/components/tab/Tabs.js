/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState, Fragment} from "react";
import NavigationComponent from "~portal-engine/scripts/components/tab/Navigation";

export default function ({style, classNames = {}, children, Navigation = NavigationComponent, ...props}) {
    if (!children.length) {
        return null;
    }

    children = children.filter(v => !!v);

    const [currentTab, setCurrentTab] = useState(children[0]);

    // tabs might have changed, check if the current tab is still available
    const found = children.find((child) => child.props.tab === currentTab.props.tab);
    if (!found) {
        setCurrentTab(children[0]);
    }

    return (
        <Fragment>
            <div className={classNames.container} style={style}>
                <Navigation classNames={classNames} setCurrentTab={setCurrentTab} tabs={children} currentTab={currentTab} {...props}/>

                <div className={`tab-content`}>
                    <div className="tab-pane active" role="tabpanel">
                        <div className={classNames.content}>
                            {children.map((child) => {
                                if(child.props.tab !== currentTab.props.tab) {
                                    return null;
                                }

                                return child.props.children;
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </Fragment>
    );
}