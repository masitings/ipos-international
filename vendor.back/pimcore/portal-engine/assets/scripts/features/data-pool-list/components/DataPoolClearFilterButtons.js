/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {
    getSelectedFilterValues,
    getSelectedTagIds,
    getSelectedFolderPath, getNavigationType
} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import {connect} from "react-redux";
import {
    clearAllFilters,
    clearFilter
} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {useTranslation} from "~portal-engine/scripts/components/Trans";
import ClearFilterButton from "~portal-engine/scripts/components/filter/clear/ClearFilterButton";
import ClearAllFilterButton from "~portal-engine/scripts/components/filter/clear/ClearAllFilterButton";
import React from "react";
import {getConfig} from "~portal-engine/scripts/utils/general";
import {TAGS} from "~portal-engine/scripts/consts/list-navigation-types";
import DataPoolClearTagFilterButtons
    from "~portal-engine/scripts/features/data-pool-list/components/DataPoolClearTagFilterButtons";
import DataPoolClearFolderFilterButtons
    from "~portal-engine/scripts/features/data-pool-list/components/DataPoolClearFolderFilterButtons";

const rootPath = getConfig('list.folders.root.path') || '/';

export const mapStateToProps = state => {
    return ({
        filters: getSelectedFilterValues(state)
            .filter(filter => filter.value && filter.label),
        showTags: getNavigationType(state) === TAGS,
        tagIds: getSelectedTagIds(state),
        folderPath: getSelectedFolderPath(state) || rootPath
    });
};

export default connect(mapStateToProps)(ClearFilterButtons)

export function ClearFilterButtons({
    filters = [],
    tagIds = [],
    showTags = false,
    folderPath = rootPath,
    dispatch
}) {
    const label = useTranslation('filter.clear-label');
    const showClearAllButton = filters.length
        || (showTags && tagIds.length)
        || (!showTags && folderPath !== rootPath);

    return (filters.length || showTags || showClearAllButton)
        ? (
            <div role="group" aria-label={label} className="vertical-gutter vertical-gutter--1">
                {filters.map((filter) => (
                    <ClearFilterButton key={`${filter.name}-${filter.value}`}
                                       onClick={() => dispatch(clearFilter(filter))}
                                       className="vertical-gutter__item mr-2">
                        {filter.label}
                    </ClearFilterButton>
                ))}

                {showTags
                    ? (<DataPoolClearTagFilterButtons/>)
                    : (<DataPoolClearFolderFilterButtons/>)
                }

                {(showClearAllButton) ? (
                    <ClearAllFilterButton onClick={() => dispatch(clearAllFilters())}
                                          className="vertical-gutter__item"/>
                ) : null}
            </div>
        ) : null
}
