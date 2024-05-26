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
import {connect} from "react-redux";
import {directDownloadClicked} from "~portal-engine/scripts/features/download/download-actions";
import {extractStyle, extractLabel, extractDownloadTypeAttributes} from "~portal-engine/scripts/features/element/element-layout";
import EmptyImage from "~portal-engine/scripts/components/EmptyImage";
import {ReactComponent as DownloadIcon} from "~portal-engine/icons/download";
import {ReactComponent as OpenIcon} from "~portal-engine/icons/external-link-alt";
import Trans from "~portal-engine/scripts/components/Trans";

export const mapDispatchToProps = (dispatch, {layout}) => ({
    downloadImage: (id, dataPoolId) => dispatch(directDownloadClicked({
        ids: [id],
        dataPoolId: dataPoolId,
        attributes: extractDownloadTypeAttributes(layout)
    }))
});

export const ImageWrapper = connect(null, mapDispatchToProps)(function ({downloadImage, label, config, children, className = ''}) {
    let content = children;
    let open = null;
    let download = null;

    if(config && config.url) {
        open = (
            <a href={config.url} className="btn icon-btn action-bar__item">
                <span className="action-bar__item__title text-nowrap">
                    <Trans t={"open"} domain="action-bar"/>
                </span>
                <OpenIcon width="12" height="12"/>
            </a>
        );
    }

    if(config && config.dataPoolId && config.downloadId) {
        download = (
            <button className="btn icon-btn action-bar__item" onClick={() => downloadImage(config.downloadId, config.dataPoolId)}>
                <span className="action-bar__item__title text-nowrap">
                    <Trans t={"download"} domain="action-bar"/>
                </span>
                <DownloadIcon width="12" height="12"/>
            </button>
        );
    }

    if(open || download) {
        content = (
            <div className={`data-type data-type--image position-relative media text-center ${className}`}>
                <div className="media__options">
                    <div className="row align-items-center text-left">
                        <div className="col-4 pr-0">
                            <div className="media__label">{label}</div>
                        </div>
                        <div className="col-8">
                            <div className="action-bar justify-content-end">
                                {open} {download}
                            </div>
                        </div>
                    </div>
                </div>

                <div className="media__item">
                    {content}
                </div>
            </div>
        );
    }

    return content;
});

export function BasicImage({className = '', src, alt, title, style}) {
    let content = (<EmptyImage style={style}/>)

    if (src) {
        content = (<img className={`img-fluid ${className}`} src={src} alt={alt} title={title} style={style}/>);
    }

    return content;
}

export default function ({layout, data, language, extractData, renderValue, className = ''}) {
    let extracted = extractData(data, layout.name, language);

    if(!extracted) {
        return renderValue(layout, null, className)
    }

    const src = extracted.thumbnail || (extracted.path + extracted.filename);
    const alt = extracted.filename;
    const title = extracted.filename;

    return (
        <ImageWrapper className={className} config={extracted} label={extractLabel(layout)} layout={layout}>
            <BasicImage src={src} alt={alt} title={title} style={extractStyle(layout)}/>
        </ImageWrapper>
    );
}