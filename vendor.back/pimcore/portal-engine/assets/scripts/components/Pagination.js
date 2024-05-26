/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from 'react';
import {noop} from "~portal-engine/scripts/utils/utils";
import {ReactComponent as ChevronRight} from "~portal-engine/icons/chevron-right";
import {ReactComponent as ChevronLeft} from "~portal-engine/icons/chevron-left";

export default function Pagination({
    pageCount = 5,
    currentPage = 1,
    className = '',
    onPageClick = noop
}) {

    let pages,
        pageSize = 3,
        startPage = 1;


    // calculate start and end item indexes
    let startIndex = currentPage === 1 ? 0 : currentPage - 2;
    let endIndex = Math.min(startIndex + pageSize, pageCount);


    if (currentPage === pageCount && pageCount >= pageSize) {
        startIndex = currentPage - pageSize;
    }

    if (startIndex + 2 === endIndex) {
        startIndex = startIndex -1;
    }

    pages = [...Array(pageCount).keys()].map(i => startPage + i);
    pages = pages.slice(startIndex, endIndex);

    return (
        <ul className={`pagination ${className}`}>
            <Fragment>
                <li className={`page-item ${currentPage === startPage ? 'disabled' : null}`}>
                    <button type="button"
                            className="page-link"
                            onClick={() => onPageClick(currentPage -1)}>

                        <ChevronLeft width="14" height="14"/>
                    </button>
                </li>

                {pages[0] === startPage + 1 ? (
                    <li className={`page-item`}>
                        <button type="button"
                                className="page-link"
                                onClick={() => onPageClick(startPage)}>
                            {startPage}
                        </button>
                    </li>
                ): null}

                {pages[0] >= startPage + 2 ? (
                    <Fragment>
                        <li className={`page-item`}>
                            <button type="button"
                                    className="page-link"
                                    onClick={() => onPageClick(startPage)}>
                                {startPage}
                            </button>
                        </li>
                        <li className="page-item d-flex align-items-center">
                            <span className="page-item__dot d-inline-block"/>
                            <span className="page-item__dot d-inline-block"/>
                            <span className="page-item__dot d-inline-block"/>
                        </li>
                    </Fragment>
                ) : null }
            </Fragment>

            {pages.map((item) => (
                <li key={item} className={`page-item ${item === currentPage ? 'active' : ''}`}>
                    <button type="button" className="page-link" onClick={() => onPageClick(item)}>
                        {item}
                        {item === currentPage ? (
                            <span className="sr-only">(current)</span>
                        ) : null}
                    </button>
                </li>
            ))}

            <Fragment>

                {pageCount >= endIndex + 1 ? (
                    <li className="page-item d-flex align-items-center">
                        <span className="page-item__dot d-inline-block"/>
                        <span className="page-item__dot d-inline-block"/>
                        <span className="page-item__dot d-inline-block"/>
                    </li>
                ) : null }

                <li className={`page-item ${currentPage === pageCount ? 'disabled' : null}`}>
                    <button type="button"
                            className="page-link"
                            onClick={() => onPageClick(currentPage + 1)}>

                        <ChevronRight width="14" height="14"/>
                    </button>
                </li>
            </Fragment>
        </ul>
    )
}