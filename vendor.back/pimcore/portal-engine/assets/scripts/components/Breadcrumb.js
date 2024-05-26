/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from 'react';

export default function ({className = "", breadcrumbs = []}) {
    return (
        <nav aria-label="breadcrumb">
            <ol className={`breadcrumb ${className}`}>
                {breadcrumbs.map((item, i) => (
                    <li key={i} className={`breadcrumb-item ${i === (breadcrumbs.length - 1) ? "active" : ""}`}>
                        <a href={item.url}>
                            {item.label}
                        </a>
                    </li>
                ))}
            </ol>
        </nav>
    );
}