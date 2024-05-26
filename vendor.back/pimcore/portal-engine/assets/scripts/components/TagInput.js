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
import Select, {components} from "react-select";
import {ReactComponent as Close} from "~portal-engine/icons/close";
import {useTranslation} from "~portal-engine/scripts/components/Trans";

function Control(props) {
    return (
        <components.Control className="form-control form-control-rounded" {...props}/>
    );
}

function MultiValueContainer() {
    return null;
}

function IndicatorsContainer() {
    return null;
}

function Placeholder() {
    return null;
}

function Input(props) {
    return (
        <components.Input {...props} placeholder={useTranslation("tags-placeholder", "asset")}/>
    );
}

const styles = {
    control: () => ({}),
    valueContainer: () => ({}),
    input: () => ({})
};

function TagInput({tags, selected, onChange = () => {}}) {
    const [value, setValue] = useState(selected);

    return (
        <Fragment>
            <div className="row row-gutter--2 vertical-gutter--2">
                {value ? value.map(tag => (
                    <div className="col-auto vertical-gutter__item" key={tag.value}>
                        <button type="button" className="btn btn-sm btn-rounded btn-dark">
                            {tag.label} <Close height="10" className="ml-2" onClick={() => {
                                const values = value.filter(val => val.value !== tag.value);
                                setValue(values);
                                onChange(values)
                            }}/>
                        </button>
                    </div>
                )) : null}
            </div>

            <div className="mt-3">
                <Select
                    className="react-select"
                    classNamePrefix={`react-select`}
                    styles={styles}
                    options={tags}
                    value={value}
                    isMulti={true}
                    backspaceRemovesValue={false}
                    onChange={(values) => {
                        setValue(values);
                        onChange(values);
                    }}
                    components={{Control, MultiValueContainer, IndicatorsContainer, Placeholder, Input}}
                />
            </div>
        </Fragment>
    );
}

export default TagInput;