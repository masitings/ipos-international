/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {showError} from "~portal-engine/scripts/utils/general";
import {fetchUnfilteredTags, fetchUsedTags} from "~portal-engine/scripts/features/tags/tags-api";
import {createAction} from "@reduxjs/toolkit";
import {createFetchActions} from "~portal-engine/scripts/utils/fetch";

export const TAGS_REQUESTED = "tags/requested";
export const TAGS_FETCHED = "tags/fetched";
export const TAGS_FAILED = "tags/failed";

export const {
    actionTypes: TAGS_FETCHING_TYPES,
    actionCreator: requestTags
} = createFetchActions(
    'tags/fetching',
    () => fetchUsedTags().response
);

export const {
    actionTypes: UNFILTERED_TAGS_FETCHING_TYPES,
    actionCreator: requestUnfilteredTags
} = createFetchActions(
    'tags/all/fetching',
    () => fetchUnfilteredTags().response
);



export const toggleTagCollapseState = createAction('tags/toggle-collapse-state');