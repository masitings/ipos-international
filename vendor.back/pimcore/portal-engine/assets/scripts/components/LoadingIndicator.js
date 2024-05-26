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

export default function ({className = '', size = '', showText = true}) {
    return (
        <div className={`loading-indicator ${size ? `loading-indicator--${size}` : ''} ${className}`} role="status">
            <div className="loading-indicator__spinner"/>

            {showText ?
                <div className="loading-indicator__text">Loading...</div>
                : null
            }
        </div>
    );
}