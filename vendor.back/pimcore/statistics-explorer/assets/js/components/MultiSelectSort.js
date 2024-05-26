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
import Select, { components } from 'react-select';
import { SortableContainer, SortableElement } from 'react-sortable-hoc';

function arrayMove(array, from, to) {
    array = array.slice();
    array.splice(to < 0 ? array.length + to : to, 0, array.splice(from, 1)[0]);
    return array;
}

const SortableMultiValue = SortableElement(props => {
    // this prevents the menu from being opened/closed when the user clicks
    // on a value to begin dragging it. ideally, detecting a click (instead of
    // a drag) would still focus the control and toggle the menu, but that
    // requires some magic with refs that are out of scope for this example
    const onMouseDown = e => {
        e.preventDefault();
        e.stopPropagation();
    };
    const innerProps = { onMouseDown };
    return <components.MultiValue {...props} innerProps={innerProps} />;
});
const SortableSelect = SortableContainer(Select);

export default function MultiSelectSort(props) {
    const onSortEnd = ({ oldIndex, newIndex }) => {
        const newValue = arrayMove(props.value, oldIndex, newIndex);
        props.onChange(newValue);
        // setSelected(newValue);
        console.log('Values sorted:', newValue.map(i => i.value));
    };

    return (
        <SortableSelect
            // react-sortable-hoc props:
            axis="xy"
            onSortEnd={onSortEnd}
            distance={4}
            // small fix for https://github.com/clauderic/react-sortable-hoc/pull/352:
            getHelperDimensions={({ node }) => node.getBoundingClientRect()}
            // react-select props:
            isMulti
            options={props.options}
            value={props.value}
            onChange={props.onChange}
            components={{
                MultiValue: SortableMultiValue,
            }}
            closeMenuOnSelect={true}
            className={props.className}
            placeholder={props.placeholder}
            name={props.name}
        />
    );
}
