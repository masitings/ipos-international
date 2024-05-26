/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from 'react';
import {connect} from "react-redux";
import Checkbox from "~portal-engine/scripts/components/Checkbox";
import ReactSelect from "react-select";
import {getDownloadAttributeById} from "~portal-engine/scripts/features/selectors";
import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import Fieldset from "~portal-engine/scripts/components/Fieldset";
import Input from "~portal-engine/scripts/features/download/components/DownloadConfig/DownloadAttribute/Input";
import Select from "~portal-engine/scripts/features/download/components/DownloadConfig/DownloadAttribute/Select";

export const mapStateToProps = (state, {
    attributeId,
    dataPoolId,
    filterOptions = () => true
}) => {
    let attribute = getDownloadAttributeById(state, {
        id: attributeId,
        dataPoolId
    });
    
    return ({
        ...attribute,
        options: attribute.formats
            .filter(filterOptions)
            .map((format) => ({...format, value: format.id}))
    });
};

export function Attribute({
    options,
    label,
    isSelected = false,
    selectedFormat,
    setup,
    onSelectionChanged,
    onFormatChanged,
    onSetupChanged
}) {
    options = options.map(option => ({
        ...option,
        label: <Trans t={option.label} domain="download-format"/>
    }));

    let selectOption = selectedFormat
        ? options.find(({value}) => value === selectedFormat)
        : options[0];

    label = useTranslation(label, 'download-type');
    
    const content = (
        <div className="row align-items-center">
            <div className="col-6">
                <div className="vertical-gutter__item">
                    <Checkbox checked={isSelected} onChange={onSelectionChanged} label={label}/>
                </div>
            </div>
            <div className="col-6">
                {(options && options.length) ? (
                    <ReactSelect className="react-select react-select--sm"
                                 classNamePrefix={`react-select`}
                                 value={selectOption}
                                 options={options}
                                 isDisabled={options.length < 2}
                                 onChange={onFormatChanged}/>
                ) : null}
            </div>
        </div>
    );

    if (!selectOption || !selectOption.setup) {
        return content;
    }

    return (
        <Fragment>
            {content}

            <Fieldset className="mt-2">
                <div className="row">
                    {Object.entries(selectOption.setup).map(([key, option]) => {
                        const Attribute = option.type === "select" ? Select : Input;

                        return (
                            <div className="col-6" key={key}>
                                <Attribute
                                    label={<Trans t={key}/>}
                                    value={setup && setup[key] ? setup[key] : ''}
                                    attribute={key}
                                    config={option}
                                    onChange={onSetupChanged}
                                />
                            </div>
                        )
                    })}
                </div>
            </Fieldset>
        </Fragment>
    );
}

export default connect(mapStateToProps)(Attribute);