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
import {getDetailData, getDetailMetadata} from "~portal-engine/scripts/features/asset/asset-selectors";
import EditMetaData from "~portal-engine/scripts/features/asset/components/metadata/EditMetaData";
import {fetchVersionHistory} from "~portal-engine/scripts/features/asset/asset-actions";

export const mapStatToProps = (state) => ({
    detail: getDetailData(state),
    metadata: getDetailMetadata(state)
});

export const mapDispatchToProps = (dispatch) => ({
    fetchVersionHistory: () => dispatch(fetchVersionHistory())
});

export function MetaData({detail, metadata, PreviewComponent, fetchVersionHistory}) {
    return (
        <div className="row vertical-gutter vertical-gutter--4">
            {PreviewComponent ? (
                <div className="col-12 col-md-3 vertical-gutter__item">
                    <PreviewComponent detail={detail}/>
                </div>
            ) : null}

            <div className={`col-12 ${PreviewComponent ? "col-md-9" : ""} vertical-gutter__item`}>
                <EditMetaData id={detail.id} readOnly={!detail.permissions.edit} setupMetadata={metadata} saveCallback={fetchVersionHistory}/>
            </div>
        </div>
    );
}

export default connect(mapStatToProps, mapDispatchToProps)(MetaData);