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
import SimpleReactLightbox, {SRLWrapper} from "simple-react-lightbox";

function ImagePreview({detail}) {
    const options = {
        buttons: {
            showAutoplayButton: false,
            showDownloadButton: false,
            showNextButton: false,
            showPrevButton: false,
            showThumbnailButton: false
        },

        caption: {
            showCaption: false
        },

        thumbnails: {
            showThumbnails: false
        }
    }

    return (
        <SimpleReactLightbox>
            <SRLWrapper options={options}>
                <a href={detail.fullPath} data-attribute="SRL">
                    <img src={detail.thumbnail} className="w-100"/>
                </a>
            </SRLWrapper>
        </SimpleReactLightbox>
    )
}

export default ImagePreview;