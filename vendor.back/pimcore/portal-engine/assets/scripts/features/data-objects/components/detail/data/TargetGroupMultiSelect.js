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
import {createMultiSelectComponent, commaSeparateValues} from "~portal-engine/scripts/features/data-objects/components/detail/data/MultiSelect";
import {extractTargetGroupSelectLabel} from "~portal-engine/scripts/features/data-objects/components/detail/data/TargetGroup";

export default createMultiSelectComponent(extractTargetGroupSelectLabel, commaSeparateValues);