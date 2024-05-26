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
import {asset} from "~portal-engine/scripts/features/data-objects/components/detail/data/Video";

function VideoPreview({detail}) {
    const preview = detail.preview;

    return asset(preview.data, detail.thumbnail, null, null, "w-100")
}

export default VideoPreview;