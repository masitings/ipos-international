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
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import {useTranslation} from "~portal-engine/scripts/components/Trans";

export default function ClearFilterButton({
    children,
    className,
    ...props
}) {
    let iconLabel = useTranslation('filter.clear');

    return (
        <button
            type="button"
            className={`btn btn-sm btn-secondary btn-rounded ${className}`}
            {...props}>
            {children}
            <CloseIcon width="12" height="12" className="ml-2" aria-label={iconLabel} title={iconLabel}/>
        </button>
    )
}