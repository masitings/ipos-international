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
import Trans from "~portal-engine/scripts/components/Trans";
import {useDropzone} from "react-dropzone";
import {showError} from "~portal-engine/scripts/utils/general";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";

export default function DropZone({
    files = [],
    dropzoneOptions = {},
    translationPrefix = '',
    onFilesChanged = noop,
    multi = true
}) {
    const onDrop = useCallback((acceptedFiles) => {
        if(!multi && acceptedFiles.length) {
            acceptedFiles = [acceptedFiles[0]];
        }

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

                reader.readAsArrayBuffer(file)
            }));

        Promise.all(readerPromises)
            .then(onFilesChanged)
            .catch(showError) /*todo*/
    }, []);

    const {getRootProps, getInputProps, isDragActive} = useDropzone({
        ...dropzoneOptions,
        onDrop
    });

    return (
        <div className={`drop-zone ${isDragActive ? 'drop-zone--drag-active' : ''} `} {...getRootProps()}>
            {files.length ? (
                <section className="drop-zone__selection">
                    <h5 className="drop-zone__selection-title">
                        <Trans t={`${translationPrefix}.drop-zone.selected-files-title`}/>
                    </h5>

                    <ul className="drop-zone__selection-list list-unstyled vertical-gutter--1">
                        {files.map(file => (
                            <li className="drop-zone__selection-list-item vertical-gutter__item text-break"
                                key={file.path}>{file.path}</li>
                        ))}
                    </ul>
                </section>
            ) : null}

            <input aria-labelledby="file-upload-input-label" {...getInputProps()} />

            <div id="file-upload-input-label" className="drop-zone__label">
                {isDragActive
                    ? <Trans t={`${translationPrefix}.drop-zone.drop`}/>
                    : <Media queries={{
                        small: MD_DOWN,
                    }}>
                        {matches => (
                            matches.small
                                ? <Trans t={`${translationPrefix}.drop-zone.placeholder-xs`}/>
                                : <Trans t={`${translationPrefix}.drop-zone.placeholder`}/>
                        )}
                    </Media>
                }
            </div>
        </div>
    )
}