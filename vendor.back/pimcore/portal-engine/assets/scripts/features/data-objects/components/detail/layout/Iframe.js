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
import {extractStyle} from "~portal-engine/scripts/features/element/element-layout";
import {buildParams} from "~portal-engine/scripts/utils/fetch";
import {getDataObjectId} from "~portal-engine/scripts/features/data-objects/data-object-selectors";

export const mapStateToProps = state => ({
    dataObjectId: getDataObjectId(state)
});

export function Iframe ({dataObjectId, layout, className = ''}) {
    const url = layout.iframeUrl + "?" + buildParams({
        "objectId": dataObjectId,
        "renderingData": layout.renderingData
    });

    const height = layout.height;

    return (
        <div className={`layout-type layout-type--iframe embed-responsive embed-responsive-1by1 ${className}`} style={{maxHeight: (height ? height: null)}}>
            <iframe src={url} className={`embed-responsive-item`} frameBorder={0} style={extractStyle(layout)}></iframe>
        </div>
    );
}

export default connect(mapStateToProps)(Iframe);