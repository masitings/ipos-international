/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState} from "react";
import {ReactComponent as IconLeft} from "~portal-engine/icons/chevron-left";
import {ReactComponent as IconRight} from "~portal-engine/icons/chevron-right";
import {fetchDocumentThumbnail} from "~portal-engine/scripts/features/asset/asset-api";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";

function thumbnail(id, page, setCurrentThumbnail) {
    fetchDocumentThumbnail(id, page)
        .then(response => setCurrentThumbnail(response.data.thumbnail));

    return page;
}

function DocumentPreview({detail}) {
    const [currentPage, setCurrentPage] = useState(1);
    const [currentThumbnail, setCurrentThumbnail] = useState(detail.thumbnail);
    const [loading, setLoading] = useState(false);

    const fetchThumbnail = (page) => {
        setLoading(true);
        setCurrentPage(page);

        fetchDocumentThumbnail(detail.id, page)
            .then((response) => {
                setCurrentThumbnail(response.data.thumbnail)
                setLoading(false);
            });

        return page;
    }

    const pageCount = detail.preview.pageCount;
    let controls = null;

    if(pageCount > 1) {
        controls = (
            <div className="d-flex justify-content-around align-items-center mt-3">
                {currentPage > 1 ? (
                    <button type="button" className="btn icon-btn btn-primary" onClick={() => fetchThumbnail(currentPage - 1)}>
                        <IconLeft height={14}/>
                    </button>
                ) : <span></span>}

                <span className="mx-3">
                    {currentPage} / {pageCount}
                </span>

                {currentPage < pageCount ? (
                    <button type="button" className="btn icon-btn btn-primary" onClick={() => fetchThumbnail(currentPage + 1)}>
                        <IconRight height={14}/>
                    </button>
                ) : <span></span>}
            </div>
        );
    }

    return (
        <div className="position-relative">
            {loading ? <LoadingIndicator className="position-absolute-center" showText={false}/> : null}

            <img src={currentThumbnail} className="img-fluid"/>

            {controls}
        </div>
    )
}

export default DocumentPreview;