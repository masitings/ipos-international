/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment, useEffect, useState} from "react";
import {Modal} from "react-bootstrap";
import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import {ReactComponent as WarningIcon} from "~portal-engine/icons/exclamation-circle";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import {fetchUploadedList} from "~portal-engine/scripts/features/upload/upload-api";
import {getConfig, showError} from "~portal-engine/scripts/utils/general";
import AssetUploadItem from "~portal-engine/scripts/features/assets/components/AssetUploadItem";
import {connect} from "react-redux";
import {getUploadId} from "~portal-engine/scripts/features/upload/upload-selectors";
import {requestListPage} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";

export function FinishedStep({uploadId, requestListPage}) {
    const warningText = useTranslation('upload.warning-message');
    const [isLoading, setLoading] = useState(true);
    const [items, setItems] = useState([]);
    const [errors, setErrors] = useState([]);

    useEffect(() => {
        let {response, abort} = fetchUploadedList({dataPoolId: getConfig('currentDataPool.id'), uploadId});

        response
            .then(({data}) => {
                setItems(data.entries.map(item => ({...item, id: item.assetId})));
                setErrors(data.messages);
                setLoading(false);

                requestListPage();
            })
            .catch(showError);

        return abort;
    }, []);

    return (
        <Modal.Body className="bg-light scroll-area modal-body--max-height">
            <h3 className="h4 mb-4">
                <Trans t="upload.finished-step.title"/>
            </h3>

            {isLoading ? (
                <LoadingIndicator className="my-4"/>
            ) : (
                <Fragment>
                    {errors && errors.length ? (
                        errors.map((error, index) => (
                            <div className="row row-gutter--1" key={index}>
                                <div className="col-auto">
                                    <WarningIcon className="icon-in-text mr-1"
                                                 height="1rem"
                                                 title={warningText}
                                                 aria-label={warningText}/>
                                </div>
                                <div className="col">
                                    {error}
                                </div>
                            </div>
                        ))
                    ) : null}

                    <div className="pt-1">
                        <ol className="list-unstyled vertical-gutter--3">
                            {items.map((item, index) => (
                                <li className="vertical-gutter__item" key={index}>
                                    <AssetUploadItem {...item} />
                                </li>
                            ))}
                        </ol>
                    </div>
                </Fragment>
            )}
        </Modal.Body>
    )
}

export const mapStateToProps = state => ({
    uploadId: getUploadId(state)
});

export const mapDispatchToProps = {
    requestListPage
};

export default connect(mapStateToProps, mapDispatchToProps)(FinishedStep)