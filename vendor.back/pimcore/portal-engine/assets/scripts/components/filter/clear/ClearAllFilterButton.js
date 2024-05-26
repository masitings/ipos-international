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
import {useTranslation} from "~portal-engine/scripts/components/Trans";

export default function ClearAllFilterButton({className, ...props}) {
    let label = useTranslation('filter.clear-all');

    return (
        <button type="button" {...props} className={`btn btn-link font-weight-bold ${className}`}>
            {label}
        </button>
    )
}




