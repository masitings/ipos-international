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
import ReactSelect, {components as SelectComponents} from 'react-select';
import {noop} from "~portal-engine/scripts/utils/utils";
import {useTranslation} from "~portal-engine/scripts/components/Trans";

function MultiSelect({
    currentValue,
    options = [],
    onChange = noop,
    label,
    theme,
    ...rest
}) {
    let currentValues = Array.isArray(currentValue)
        ? currentValue
        : [currentValue];

    const selectedOptions = options.filter(option => currentValues.includes(option.value)) || null;
    const changeHandler = (selectedOptions) => onChange({
        newValues: selectedOptions
            ? selectedOptions.map(({value}) => value)
            : []
    });

    return (
        <ReactSelect {...rest} onChange={changeHandler}
                     isMulti={true}
                     options={options}
                     value={selectedOptions}
                     placeholder={label}
                     aria-label={label}
                     className={`react-select react-select--sm ${theme ? `react-select--${theme}`: ''}` }
                     classNamePrefix={`react-select`}
            components={{ ValueContainer }}
        />
    );
}

function ValueContainer({...props}) {
    const {children, getValue} = props;
    let values = getValue();
    let showPlaceholder = values && values.length > 1;

    let translation = useTranslation('filter.[count]-values-selected') || '';
    let label = translation.replace('[count]', values.length);
    return showPlaceholder
        ? (
            <SelectComponents.ValueContainer {...props}>
                <span className="text-nowrap" title={label}>{label}</span>
                {children[1]}
            </SelectComponents.ValueContainer>
        )
        : (
            <SelectComponents.ValueContainer {...props}>{children}</SelectComponents.ValueContainer>
        )
}

export const Component = MultiSelect;

export const getSelectedFilterValues = (filter, filterState) => {
    let currentValue = filterState.currentValue || [];
    return currentValue.map(currentValue => {
        let currentOption = filterState.options.find((option) => (
            option.value === currentValue
        ));

        return ({
            name: filter.name,
            value: currentValue,
            label: currentOption.label
        });
    })
};

export function getSerializeName(filter) {
    return `${filter.name}[]`;
}

export function serialize(filter, filterState) {
    let currentValues = filterState.currentValue || [];
    return currentValues.map(currentValue => [
        getSerializeName(filter),
            currentValue,
        ]);
}

export const deserialize = (filter, params) => ({
    currentValue: params
        .filter(([name]) => name === getSerializeName(filter))
        .map(([name, value]) => value)
});

export const update = (state, {newValues}) => ({
    currentValue: newValues
});

export const clear = (state, {value}) => ({
    currentValue: state.currentValue.filter(currentValue => currentValue !== value)
});

export const clearAll = state => ({
    currentValue: []
});