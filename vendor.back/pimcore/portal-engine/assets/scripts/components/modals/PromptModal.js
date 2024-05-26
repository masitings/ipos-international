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
import {noop} from "~portal-engine/scripts/utils/utils";
import Trans from "~portal-engine/scripts/components/Trans";
import {useConfirmModal} from "~portal-engine/scripts/components/modals/ConfirmModal";

function Prompt({onChange, value = '', label}) {

    const inputRef = React.createRef();

    useEffect(() => {
        inputRef.current.parentNode.focus();
    });

    return (
        <div>
            <label className="form-group d-block">

                {label ? label : <Trans t="prompt-modal.label"/>}
                <input value={value}
                       ref={inputRef}
                       onChange={(evt) => onChange(evt.target.value)}
                       type="text"
                       autoFocus={true}
                       className="form-control"/>
            </label>
        </div>
    )
}

export const usePromptModal = (callback = noop, {label, ...options}) => {
    const [value, setValue] = useState('');

    const {confirm, confirmModal: promptModal} = useConfirmModal(
        (...params) => callback(value, ...params),
        {
            title: <Trans t="prompt-modal.title"/>,
            message: (
                <Prompt label={label} value={value} onChange={setValue}/>
            ),
            cancelText: <Trans t="prompt-modal.cancel"/>,
            confirmText: <Trans t="prompt-modal.confirm"/>,
            ...options,
        }
    );

    return {
        prompt: (defaultValue, ...params) => {
            setValue(defaultValue);
            confirm(...params);
        },
        promptModal
    }
};