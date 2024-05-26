/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState, useEffect} from "react";
import {mapObject, noop} from "~portal-engine/scripts/utils/utils";
import ActionBar from "~portal-engine/scripts/components/actions/ActionBar";
import {CSSTransition} from 'react-transition-group'
import Trans from "~portal-engine/scripts/components/Trans";

export default function (props) {
    let {
        className = "",
        isLoading = false,
        onDeSelectAll = noop,
        onShowSelected = noop,
        actionHandler = {},
        selectedIds = [],
        ListComponent,
    } = props;

    const [showItems, setShowItems] = useState(false);

    useEffect(() => {
        if (selectedIds.length <= 0 || selectedIds.length >= 1000) {
            setShowItems(false);
        }
    }, [selectedIds]);

    // call all action handler with selectedIds as parameter
    actionHandler = mapObject(actionHandler, (_, fnc) => (() => fnc(selectedIds)));

    return (
        <CSSTransition
            classNames="selection-bar-"
            in={selectedIds.length > 0}
            unmountOnExit={true}
            timeout={120} appear>
            <div className={`selection-bar ${className} `}>
                <div className="container">
                    <div className="row vertical-gutter--3 align-items-center">
                        <div className="col-md col-xl-4 vertical-gutter__item">
                            <div className="row align-items-center">
                                <div className="col col-md-auto">
                                    <div className="text-muted font-weight-bold">
                                        <span className="state-circle d-inline-block bg-success mr-1"/>
                                        {selectedIds.length} <Trans t={selectedIds.length === 1 ? 'selection-bar.item': 'selection-bar.items'}/> <span className="d-none d-md-inline-block"><Trans t='selection-bar.selected'/></span>
                                    </div>
                                </div>
                                {selectedIds.length < 1000 ? (
                                    <div className="col col-md-auto text-center">
                                        <button type="button" className="btn btn-link font-weight-bold selection-bar__btn"
                                                onClick={() => {setShowItems(!showItems); onShowSelected(selectedIds)}}>
                                            {showItems ? (
                                                <Trans t="selection-bar.hide-all"/>
                                            ) : (
                                                <Trans t="selection-bar.show-all"/>
                                            )}
                                        </button>
                                    </div>
                                ) : null}
                                <div className="col col-md-auto text-center">
                                    <button type="button"
                                            className="btn btn-link font-weight-bold selection-bar__btn"
                                            onClick={() => onDeSelectAll(selectedIds)}>
                                        <Trans t="selection-bar.de-select-all"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-auto col-xl-4 vertical-gutter__item">
                            <ActionBar className="action-bar--light"
                                       isLarge="true"
                                       actionHandler={actionHandler}
                                       />
                        </div>
                    </div>
                </div>
                {selectedIds.length > 0 && selectedIds.length < 1000 ? (
                    <CSSTransition
                        classNames="selection-bar__list-"
                        in={showItems}
                        unmountOnExit={true}
                        timeout={120}
                    >

                        <div className="selection-bar__list">
                            <div className="container">
                                <ListComponent ids={selectedIds} isLoading={isLoading}/>
                            </div>
                        </div>
                    </CSSTransition>
                ) : null }
            </div>
        </CSSTransition>
    );
}