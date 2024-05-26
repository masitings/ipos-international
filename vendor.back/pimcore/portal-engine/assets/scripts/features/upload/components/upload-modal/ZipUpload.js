/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import DropZone from "~portal-engine/scripts/components/DropZone";
import React from "react";

export default function ZipUpload(props) {
    return <DropZone
        translationPrefix={'upload.zip'}
        dropzoneOptions={{
            multiple: false,
            accept: '.zip'
        }}
        {...props}
    />
}