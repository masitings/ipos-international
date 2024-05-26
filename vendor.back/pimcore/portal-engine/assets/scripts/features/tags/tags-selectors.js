/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {createSelector} from "@reduxjs/toolkit";

export const getFetchingState = state => state.fetchingState;
export const getUnfilteredFetchingState = state => state.fetchingStateUnfiltered;
export const getById = (state, id) => state.byId[id];
export const getAllTags = (state) => state.byId;
export const getAllIds = (state) => state.allIds;
export const getUnfilteredIds = (state) => state.unfilteredIds;
export const isOpenById = (state, path) => getById(state, path).isOpen || false;
export const getSelectedIds = (state) => state.selectedIds || [];

export const getChildIdsById = (state, parentId) =>
    getAllIds(state)
        .filter(id => {
            let node = getById(state, id);
            return node && node.parent === parentId
        })
    || [];

function renderOptionLabel(tags, tag, name) {
    let tagLabel = tag.name;

    if (tag.parent) {
        let parent = tags[tag.parent];
        let currentName = parent.name + ' > ' + name;

        tagLabel = renderOptionLabel(tags, parent, currentName)
    } else {
        tagLabel = name;
    }

    return tagLabel;
}

export const getUnfilteredIdsAsOptions = createSelector(
    [getUnfilteredIds, getAllTags],
    (tagIds, tags) => {
        return tagIds.map((id) => {
            const tag = tags[id];
            const tagLabel = renderOptionLabel(tags, tag, tag.name);

            return {
                id: tag.id,
                value: tag.id,
                label: tagLabel,
                selectedLabel: tag.name
            }
        });
    }
);