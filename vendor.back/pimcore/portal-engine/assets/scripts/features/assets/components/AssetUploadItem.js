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
import {connect} from "react-redux";
import {useTranslation} from "~portal-engine/scripts/components/Trans";
import ActionBar from "~portal-engine/scripts/components/actions/ActionBar";
import {ReactComponent as WarningIcon} from "~portal-engine/icons/exclamation-circle";

export function AssetUploadItem({
    id,
    name,
    fullPath,
    message,
    actionHandler = {},
    actionUrls = {}
}) {
    const warningText = useTranslation('upload.warning-message');

    return (
        <Fragment>
            <div className="row align-items-center">
                {fullPath ? (
                    <div className="col-3 col-md-2">
                        <div className="embed-responsive embed-responsive-16by9">
                            <div className="embed-responsive-item blur-image text-center">
                                <div className="blur-image__bg" style={{backgroundImage: `url(${fullPath})`}}/>
                                <img src={fullPath} alt={name} className="position-relative blur-image__image"/>
                            </div>
                        </div>
                    </div>
                ): null}

                <div className="col-7">
                    <div className="text-break">
                        {name}
                    </div>
                </div>

                {id ? (
                    <div className="col-auto">
                        <ActionBar actionUrls={actionUrls} actionUrlsTarget="_blank" actionHandler={actionHandler}/>
                    </div>
                ): null}
            </div>

            {message ? (
                <div className="row row-gutter--1">
                    <div className="col-auto">
                        <WarningIcon className="icon-in-text mr-1"
                                     height="1rem"
                                     title={warningText}
                                     aria-label={warningText}/>
                    </div>
                    <div className="col">
                        {message}
                    </div>
                </div>
            ): null}
        </Fragment>

    )
}

export default connect(null, null)((props) => {
    let transformedProps = {
        ...props,
        actionUrls: {
            onDetail: props.detailLink
        }
    };

    return (
        <AssetUploadItem {...transformedProps}/>
    );
});