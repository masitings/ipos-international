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
import {directAssetDownload} from "~portal-engine/scripts/features/download/download-api";
import {ReactComponent as DownloadIcon} from "~portal-engine/icons/download";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";

function DownloadButton({id, label, thumbnail = null}) {
    return (
        <ButtonWithIcon
            type="button"
            variant="primary"
            className={"mx-2 vertical-gutter__item"}
            Icon={<DownloadIcon width="16" height="16"/>}
            onClick={() => directAssetDownload(id, thumbnail)}
        >
            {label}
        </ButtonWithIcon>
    );
}

export default DownloadButton;