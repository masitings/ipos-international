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
import ReactSelect from 'react-select';
import {noop} from "~portal-engine/scripts/utils/utils";

function Select({
    currentValue,
    options = [],
    onChange = noop,
    label,
    theme,
    ...rest
}) {
    const selectedOption = options.find(option => option.value === currentValue) || null;

    const changeHandler = (selectedOption, {action}) => {
        if (action === "clear") {
            onChange({newValue: null})
        } else {
            onChange({newValue: selectedOption.value})
        }
    };

    return (
        <ReactSelect {...rest}
             onChange={changeHandler}
             options={options}
             value={selectedOption}
             placeholder={label}
             aria-label={label}
             isClearable={true}
             className={`react-select react-select--sm ${theme ? `react-select--${theme}`: ''}` }
             classNamePrefix={`react-select`}
        />
    );
}

export const Component = Select;

export function getSelectedFilterValues(filter, filterState) {
    let currentOption = filterState.options.find((option) => (
        option.value === filterState.currentValue
    ));

    if (currentOption) {
        return [{
            name: filter.name,
            value: filterState.currentValue,
            label: currentOption.label
        }]
    }

    return [];
}

export function getSerializeName(filter) {
    return filter.name;
}

export function serialize(filter, filterState) {
    return [getSerializeName(filter), filterState.currentValue];
}

export function deserialize(filter, params) {
    let matchingParam = params
        .find(([name]) => name === getSerializeName(filter));

    return {
        currentValue: (matchingParam && matchingParam.length)
            ? matchingParam[1]
            : null
    }
}

export const update = (state, {newValue}) => ({
    currentValue: newValue
});

export const clear = () => ({
    currentValue: null
});
export const clearAll = () => ({
    currentValue: null
});