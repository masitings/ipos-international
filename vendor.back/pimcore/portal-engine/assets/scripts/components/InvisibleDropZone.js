/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {noop} from "~portal-engine/scripts/utils/utils";
import React, {useCallback} from "react";
import {useDropzone} from "react-dropzone";
import {showError} from "~portal-engine/scripts/utils/general";

export default function DropZone({
    dropzoneOptions = {},
    label,
    onFilesDropped = noop,
    disabled = false,
    children
}) {
    const onDrop = useCallback((acceptedFiles) => {
        let readerPromises = acceptedFiles.map((file) =>
            new Promise((resolve, reject) => {
                const reader = new FileReader();

                reader.onabort = () => reject();
                reader.onerror = () => reject();
                reader.onload = () => {
                    resolve({
                        ...file,
                        type: file.type,
                        binary: reader.result
                    });
                };

                reader.readAsArrayBuffer(file);
            }));

        Promise.all(readerPromises)
            .then(onFilesDropped)
            .catch(showError)
    }, []);

    const {getRootProps, isDragActive} = useDropzone({
        ...dropzoneOptions,
        onDrop: disabled
            ? noop
            : onDrop
    });

    return (
        <div className={`drop-zone ${disabled ? 'drop-zone--disabled': ''} drop-zone--invisible ${isDragActive ? 'drop-zone--drag-active' : ''} `} {...getRootProps()}>
            {label ? (
                <div id="file-upload-input-label" className="drop-zone__label">
                    {label}
                </div>
            ) : null}

            <div className="drop-zone__children">
                {children}
            </div>
        </div>
    )
}