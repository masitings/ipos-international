/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useEffect} from "react";
import {connect} from "react-redux";
import ReactSelect, {components as SelectComponents} from 'react-select';
import {getUnfilteredIdsAsOptions} from "~portal-engine/scripts/features/tags/tags-selectors";
import {
    getUnfilteredTagsFetchingState
} from "~portal-engine/scripts/features/selectors";
import {requestUnfilteredTags} from "~portal-engine/scripts/features/tags/tags-actions";
import {FETCHING, NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";

export const TagSelect = connect(mapStateToProps)(props => {
    const {
        currentValues,
        options = [],
        isLoading = false,
        label,
        theme,
    } = props;

    return <ReactSelect {...props}
                        isMulti={true}
                        options={options}
                        value={currentValues}
                        placeholder={label}
                        aria-label={label}
                        isLoading={isLoading}
                        className={`react-select ${theme ? `react-select--${theme}`: ''}` }
                        classNamePrefix={`react-select`}
                        components={{ MultiValueLabel }}
    />
});

export const MultiValueLabel = props => {
    return (
        <SelectComponents.MultiValueLabel {...props}>{props.data.selectedLabel}</SelectComponents.MultiValueLabel>
    );
};

export function mapStateToProps(state) {
    return {
        options: getUnfilteredIdsAsOptions(state.tags)
    }
}

// Add a wrapper to load the initial data
export default connect(
    (state) => ({
        fetchingState: getUnfilteredTagsFetchingState(state)
    })
)(({fetchingState, dispatch, ...props}) => {
    useEffect(function () {
        if (fetchingState === NOT_ASKED) {
            dispatch(requestUnfilteredTags(props.selectedIds));
        }
    }, []);


    let transformedProps = {
        ...props,
    };
    if (fetchingState === NOT_ASKED || fetchingState === FETCHING) {
        transformedProps.isLoading = true;
    }

    return (<TagSelect {...transformedProps}/>)
});