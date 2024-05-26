/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment, useState} from "react";
import {connect} from "react-redux";
import {getDetailData, getPermissions} from "~portal-engine/scripts/features/asset/asset-selectors";
import {updateDetailTags, fetchVersionHistory} from "~portal-engine/scripts/features/asset/asset-actions";
import {saveTags} from "~portal-engine/scripts/features/asset/asset-api";
import Card from "~portal-engine/scripts/components/Card";
import Trans from "~portal-engine/scripts/components/Trans";
import DownloadButtonGroup from "~portal-engine/scripts/features/asset/components/detail/preview/DownloadButtonGroup";
import DownloadButton from "~portal-engine/scripts/features/asset/components/detail/preview/DownloadButton";
import TagInput from "~portal-engine/scripts/components/TagInput";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import {ReactComponent as SaveIcon} from "~portal-engine/icons/save";
import Workflow from "~portal-engine/scripts/features/asset/components/detail/Workflow";
import {isPublicShare} from "~portal-engine/scripts/features/public-share/public-share-selectors";

export const mapStatToProps = (state) => {
    return {
        detail: getDetailData(state),
        permissions: getPermissions(state),
        isPublicShare: isPublicShare(state)
    }
};

export const mapDispatchToProps = (dispatch) => ({
    updateTags: (tags) => dispatch(updateDetailTags(tags)),
    fetchVersionHistory: () => dispatch(fetchVersionHistory())
});

export function Attributes({attributes}) {
    if (!attributes || !Object.entries(attributes).length) {
        return null;
    }

    return Object.entries(attributes).map(([attribute, value]) => (
        <div className="vertical-gutter__item" key={attribute}>
            <div className="row">
                <div className="col-5">
                    <Trans t={attribute} domain="asset"/>:
                </div>

                <div className="col-7">
                    {value}
                </div>
            </div>
        </div>
    ));
}

export function Overview({detail, updateTags, PreviewComponent, permissions, fetchVersionHistory, isPublicShare = false}) {
    const [savingTags, setSavingTags] = useState(false);
    let metadataAddition = null;

    if(permissions.edit) {
        metadataAddition = (
            <button
                type="button"
                className="btn btn-sm btn-primary btn-with-addon btn-rounded"
                onClick={() => {
                    setSavingTags(true);
                    saveTags(detail.id, detail.assignedTags)
                        .then(fetchVersionHistory)
                        .finally(() => setSavingTags(false));
                }}
            >
                {savingTags ? (
                    <Fragment>
                        <span className="btn__addon">
                            <LoadingIndicator size="inline" showText={false}/>
                        </span>
                        <Trans t="saving-tags" domain="asset"/>
                    </Fragment>
                ) : (
                    <Fragment>
                        <span className="btn__addon">
                            <SaveIcon height={14}/>
                        </span>
                        <Trans t="save-tags" domain="asset"/>
                    </Fragment>
                )}
            </button>
        );
    }

    return (
        <div className="vertical-gutter--4 row justify-content-between">
            <div className="col-md-8 vertical-gutter__item">
                <div className="vertical-gutter vertical-gutter--4">
                    <div className="vertical-gutter__item d-flex justify-content-center">
                        {PreviewComponent ? <PreviewComponent detail={detail}/> : null}
                    </div>

                    {Array.isArray(detail.downloadShortcuts) && (
                        <div className="vertical-gutter__item">
                        <DownloadButtonGroup>
                            {detail.downloadShortcuts.map((shortcut) => (
                                <DownloadButton key={shortcut} id={detail.id} thumbnail={shortcut} label={<Trans t={shortcut} domain="asset"/>}/>
                            ))}
                        </DownloadButtonGroup>
                        </div>
                    )}
                </div>
            </div>

            <div className="col-md-4 vertical-gutter__item">
                <div className="vertical-gutter vertical-gutter--3">
                    {typeof detail.attributes === "object" && Object.keys(detail.attributes).length > 0 &&
                    <Card className="vertical-gutter__item" title={(<Trans t="general" domain="asset"/>)}>
                        <div className="vertical-gutter--2">
                            <Attributes attributes={detail.attributes}/>
                        </div>
                    </Card>
                    }

                    <Card className="vertical-gutter__item" title={(<Trans t="file-description" domain="asset"/>)} id="FileDescription">
                        <div className="vertical-gutter--2">
                            <div className="vertical-gutter__item">
                                <div className="row">
                                    <div className="col-5">
                                        <Trans t="filename" domain="asset"/>:
                                    </div>
                                    <div className="col-7">
                                        {detail.filename}
                                    </div>
                                </div>
                            </div>
                            <div className="vertical-gutter__item">
                                <div className="row">
                                    <div className="col-5">
                                        <Trans t="creation-date" domain="asset"/>:
                                    </div>
                                    <div className="col-7">
                                        {detail.creationDate}
                                    </div>
                                </div>
                            </div>
                            <div className="vertical-gutter__item">
                                <div className="row">
                                    <div className="col-5">
                                        <Trans t="modification-date" domain="asset"/>:
                                    </div>
                                    <div className="col-7">
                                        {detail.modificationDate}
                                    </div>
                                </div>
                            </div>
                            <div className="vertical-gutter__item">
                                <div className="row">
                                    <div className="col-5">
                                        <Trans t="file-size" domain="asset"/>:
                                    </div>
                                    <div className="col-7">
                                        {detail.fileSize}
                                    </div>
                                </div>
                            </div>
                            <div className="vertical-gutter__item">
                                <div className="row">
                                    <div className="col-5">
                                        <Trans t="type" domain="asset"/>:
                                    </div>
                                    <div className="col-7">
                                        *.{detail.extension}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Card>

                    {detail.tags.length > 0 && (permissions.edit || detail.assignedTags?.length > 0) && (
                        <Card
                            title={(<Trans t="tags" domain="asset"/>)}
                            id="Tags"
                            className="vertical-gutter__item"
                            headerAddition={metadataAddition}
                        >
                            {permissions.edit ? (
                                <TagInput tags={detail.tags} selected={detail.assignedTags} onChange={updateTags}/>
                            ) : (
                                <Fragment>
                                    {detail.assignedTags.map((tag) => (
                                        <span className={"badge badge-secondary mr-1"}>
                                            {tag.label}
                                        </span>
                                    ))}
                                </Fragment>
                            )}
                        </Card>
                    )}

                    {!isPublicShare ? (
                        <Workflow className="vertical-gutter__item"/>
                    ): null}
                </div>

            </div>
        </div>
    );
}

export default connect(mapStatToProps, mapDispatchToProps)(Overview);