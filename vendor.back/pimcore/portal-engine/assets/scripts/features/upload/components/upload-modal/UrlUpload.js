/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {useTranslation} from "~portal-engine/scripts/components/Trans";
import FormControl from "~portal-engine/scripts/components/FormControl";
import React from "react";
import {noop} from "~portal-engine/scripts/utils/utils";

export default function UrlUpload({value, onChange = noop}) {
    const placeholder = useTranslation('upload.url.placeholder');
    const label = useTranslation('upload.url.label');

    return (
        <FormControl label={label}
                     placeholder={placeholder}
                     type="url"
                     value={value}
                     onChange={onChange}
                     onInput={onChange}
        />
    )
}
