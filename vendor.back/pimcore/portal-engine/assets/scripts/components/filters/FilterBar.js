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
import {noop} from "~portal-engine/scripts/utils/utils";
import {ReactComponent as SlidersIcon} from "~portal-engine/icons/sliders-h";
import Trans from "~portal-engine/scripts/components/Trans";
import {Collapse} from "react-collapse";

export default function FilterBar(props) {
    const [isCollapseOpen, setIsCollapseOpen] = useState(isCollapseOpen);

    const {
        className = "",
        filters = [],
        additionalEntries,
        componentsByType = {},
        onChange = noop
    } = props;

    const handleChange = (filter, payload) => onChange({
        ...filter,
        ...payload
    });

    let headFilterLimit = additionalEntries ? (5 - additionalEntries.length) : 6;
    let hasDetail = filters.length > headFilterLimit;

    let headFilters = hasDetail
        ? filters.filter((_, index) => index < headFilterLimit - 1)
        : filters;

    let detailFilters = hasDetail
        ? filters.filter((_, index) => index >= headFilterLimit - 1)
        : [];
    
    return (
        (filters.length || additionalEntries)
            ? (
                <div className={`filter-bar ${className}`}>
                    <div className="container">
                        <div className="row align-items-center">
                            {headFilters.map((filter) => {
                                const Component = componentsByType[filter.type].Component;

                                if (!Component) {
                                    console.error(`Missing Component function for type ${filter.type}`);
                                    return;
                                }

                                return (
                                    <div className="col-md-2" key={filter.name}>
                                        <Component
                                            theme={'secondary'}
                                            onChange={selectedOption => handleChange(filter, selectedOption)}
                                            {...filter} />
                                    </div>
                                );
                            })}

                            {hasDetail ? (
                                <div className="col-auto">
                                    <button type="button"
                                            className="btn btn-gray btn-with-addon"
                                            onClick={() => setIsCollapseOpen(!isCollapseOpen)}
                                            aria-controls="filter-collapse"
                                            aria-expanded={isCollapseOpen}>
                                        <span className="btn__addon">
                                            <SlidersIcon width="15" height="15"/>
                                        </span>

                                        <Trans t="filter.show-additional-filter"/>
                                    </button>
                                </div>
                            ) : null}

                            {additionalEntries ? (
                                additionalEntries.map((entry, index) => (
                                    <div key={index} className={`col-auto text-nowrap ${index === 0 ? 'ml-auto': ''}`}>
                                        {entry}
                                    </div>
                                ))
                            ) : null}
                        </div>

                        {hasDetail ? (
                            <Collapse theme={{collapse: 'collapse-container', content: `filter-bar__collapse`}}
                                      isOpened={isCollapseOpen}>
                                <div className="row">
                                    {detailFilters.map((filter) => {
                                        const Component = componentsByType[filter.type].Component;

                                        if (!Component) {
                                            console.error(`Missing Component function for type ${filter.type}`);
                                            return;
                                        }

                                        return (
                                            <div className="col-md-2" key={filter.name}>
                                                <Component
                                                    theme={'secondary'}
                                                    onChange={selectedOption => handleChange(filter, selectedOption)}
                                                    {...filter} />
                                            </div>
                                        );
                                    })}
                                </div>
                            </Collapse>
                        ) : null}
                    </div>
                </div>
            )
            : null
    );
}