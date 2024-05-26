/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {ReactComponent as FolderIcon} from "~portal-engine/icons/folder-open";
import Trans from "~portal-engine/scripts/components/Trans";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import React from "react";

export default function ({href = '/', btnProps = {}}) {
    return (
        <ButtonWithIcon href={href}
                        variant={"primary"}
                        {...btnProps}
                        Icon={<FolderIcon width="16" height="16"/>}>
            <Trans t="upload.upload-folder.open"/>
        </ButtonWithIcon>
    );
}