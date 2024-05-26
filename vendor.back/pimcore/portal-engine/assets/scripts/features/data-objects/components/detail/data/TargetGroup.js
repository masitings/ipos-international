/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from "react";
import {createSelectComponent} from "~portal-engine/scripts/features/data-objects/components/detail/data/Select";

export function extractTargetGroupSelectLabel(value) {
    return value ? value.name : null;
}

export default createSelectComponent(extractTargetGroupSelectLabel);