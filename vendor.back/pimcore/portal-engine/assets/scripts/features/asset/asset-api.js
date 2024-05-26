/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {getConfig} from "~portal-engine/scripts/utils/general";
import {
    abortAbleFetch,
    fetchJson,
    prepareFetchPromise,
    sendJson,
    buildParams
} from "~portal-engine/scripts/utils/fetch";
import {addParamsObjectToURL} from "~portal-engine/scripts/utils/utils";
import {getLanguage} from "~portal-engine/scripts/utils/intl";

const publicShareHash = getConfig('publicShare.hash');
const additionalParams = {
    ...(publicShareHash ? {publicShareHash} : null)
};

export function fetchMetadataLayout() {
    return fetchJson(addParamsObjectToURL(`/_portal-engine/api/asset/metadata-layout`, {
        dataPoolId: getConfig("currentDataPool.id"),
        ...additionalParams
    }));
}

export function fetchDocumentThumbnail(id, page) {
    return fetchJson(`/_portal-engine/api/asset/document-preview/${id}/${page}?dataPoolId=${getConfig("currentDataPool.id")}`);
}

export function saveMetadata(assetId, metadata) {
    return sendJson(`/_portal-engine/api/asset/save-metadata/${assetId}?dataPoolId=${getConfig("currentDataPool.id")}`, metadata);
}

export function saveTags(assetId, tags) {
    return sendJson(`/_portal-engine/api/asset/save-tags/${assetId}?dataPoolId=${getConfig("currentDataPool.id")}`, {tags});
}

export function fetchVersionHistory(assetId) {
    return fetchJson(`/_portal-engine/api/asset/version-history/${assetId}?dataPoolId=${getConfig("currentDataPool.id")}`);
}

export function fetchVersionComparison(assetId, versionIds) {
    return fetchJson(`/_portal-engine/api/asset/version-comparison/${assetId}?${buildParams({
        dataPoolId: getConfig("currentDataPool.id"),
        ids: versionIds
    })}`);
}

export function batchMetaData({dataPoolId, ids, metadata, tags, tagsApplyMode}) {
    let {abort, response} = abortAbleFetch(addParamsObjectToURL('/_portal-engine/api/asset/trigger-metadata-batch-update', {
        dataPoolId,
        'targetPage': window.location.pathname + window.location.search
    }), {
        headers: {
            "Content-Type": "application/json; charset=UTF-8"
        },
        method: 'POST',
        body: JSON.stringify({
            ids,
            metadata,
            tags,
            tagsApplyMode
        })
    });

    return {
        abort,
        response: prepareFetchPromise(response)
    };
}

export function fetchWorkflow(assetId) {
    return fetchJson(`/_portal-engine/api/asset/workflow/${assetId}?dataPoolId=${getConfig("currentDataPool.id")}`);
}

export function publishVersion(assetId, versionId) {
    return fetchJson(`/_portal-engine/api/asset/publish-version/${assetId}?dataPoolId=${getConfig("currentDataPool.id")}&versionId=${versionId}`);
}

export function applyWorkflowTransition(assetId, workflow, transition, type, data) {
    return sendJson(`/_portal-engine/api/asset/workflow/apply-transition/${assetId}?dataPoolId=${getConfig("currentDataPool.id")}&_locale=${getLanguage()}`, {
        workflow: workflow,
        transition: transition,
        type: type,
        data: data
    });
}

export function openDirectEdit(assetId) {
    return fetchJson(`/_portal-engine/api/direct-edit/generate_link/${assetId}`, {}, true);
}

export function getDirectEditUrl(routeName, assetId) {
    let prefix = "/_portal-engine/api/direct-edit";

    if(routeName === 'directedit_confirm') {
        return prefix + '/confirm_edit/' + assetId;
    }

    if(routeName === 'directedit_cancel') {
        return prefix + '/cancel_edit/' + assetId
    }

    if(routeName === 'directedit_confirm_overwrite_after_local_edit') {
        return prefix + '/confirm_overwrite_after_local_edit/' + assetId
    }

    if(routeName === 'directedit_confirm_versionsave_after_local_edit') {
        return prefix + '/confirm_versionsave_after_local_edit/' + assetId
    }

    return routeName;
}