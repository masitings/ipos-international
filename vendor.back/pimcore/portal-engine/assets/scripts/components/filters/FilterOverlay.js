/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment, useState} from "react";
import {noop} from "~portal-engine/scripts/utils/utils";
import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import Overlay from "~portal-engine/scripts/components/Overlay";
import {connect} from "react-redux";
import {
    getFilterStatesFetchingState,
    getResultCount
} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import {FETCHING, NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import {ReactComponent as SlidersIcon} from "~portal-engine/icons/sliders-h";

export default function FilterOverlay(props) {
    const [isOpen, setIsOpen] = useState(false);

    const {
        filters = [],
        componentsByType = {},
        additionalEntries,
        children,
        subTitle,
        onChange = noop,
        MobileToggleButtonComponent = MobileToggleButton,
        SubmitButtonComponent = SubmitButton
    } = props;

    const handleChange = (filter, payload) => onChange({
        ...filter,
        ...payload
    });

    let overlayTitle = useTranslation('filter.overlay-title');

    return (filters.length || additionalEntries || children)
        ? (
            <Fragment>
                <MobileToggleButtonComponent {...props} isOpen={isOpen} onClick={() => setIsOpen(!isOpen)}/>

                <Overlay title={overlayTitle}
                         isShown={isOpen}
                         className="filter-overlay"
                         onClose={() => setIsOpen(false)}>
                    {subTitle ? (
                        <h5>{subTitle}</h5>
                    ) : null}

                    <div className="vertical-gutter vertical-gutter--3">
                        {additionalEntries ? (
                            <div className="vertical-gutter__item">
                                <div className="row row-gutter--2 vertical-gutter--2 btn-row">
                                    {additionalEntries.map((entry, index) => (
                                        <div key={index}
                                             className={`${additionalEntries.length > 1 ? '' : ''} col vertical-gutter__item`}>
                                            {entry}
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ) : null}

                        {filters.map((filter) => {
                            const Component = componentsByType[filter.type].Component;

                            if (!Component) {
                                console.error(`Missing Component function for type ${filter.type}`);
                                return;
                            }

                            return (
                                <div className="vertical-gutter__item" key={filter.name}>
                                    <Component
                                        onChange={selectedOption => handleChange(filter, selectedOption)}
                                        {...filter} />
                                </div>
                            );
                        })}

                        {children ? (
                            <div className="vertical-gutter__item">
                                {children}
                            </div>
                        ) : null}

                        <div className="vertical-gutter__item">
                            <SubmitButtonComponent onClick={() => setIsOpen(false)}/>
                        </div>
                    </div>
                </Overlay>
            </Fragment>
        ) : null
}

export const SubmitButton = connect(state => {
    let fetchingState = getFilterStatesFetchingState(state);

    return ({
        resultCount: getResultCount(state),
        isFetching: fetchingState === FETCHING || fetchingState === NOT_ASKED,
    });
})(({
    className = '',
    resultCount = 0,
    isFetching = true,
    dispatch,
    ...props
}) => {
    let text = useTranslation(isFetching
        ? 'filter.show-results'
        : resultCount === 1 ? 'filter.show-[count]-result': 'filter.show-[count]-results'
    ) || '';

    return (
        <button type="button" className={`btn btn-primary btn-block ${className}`} {...props}>
            {text.replace('[count]', resultCount)}
        </button>
    );
});

export function MobileToggleButton(props) {
    const {
        isOpen = false,
        onClick = noop,
        className = ""
    } = props;

    return (
        <button type="button"
                onClick={onClick}
                className={`btn btn-secondary btn-block btn-rounded btn-with-addon ${className}`}
                aria-controls="filter-collapse"
                aria-expanded={isOpen}>
            <span className="btn__addon">
                <SlidersIcon width="15" height="15"/>
            </span>

            <Trans t="filter.open-overlay"/>
        </button>
    )
}