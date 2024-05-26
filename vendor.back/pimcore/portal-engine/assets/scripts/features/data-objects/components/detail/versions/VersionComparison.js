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
import {FETCHING, NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import {
    getSelectedVersionIds,
    getVersionComparison,
    getVersionComparisonFetchingState
} from "~portal-engine/scripts/features/data-objects/data-object-selectors";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import Trans from "~portal-engine/scripts/components/Trans";
import Table from "~portal-engine/scripts/components/Table";
import {LOCALIZED_FIELDS} from "~portal-engine/scripts/consts/layout";

export const mapStateToProps = state => {
    return {
        versionIds: getSelectedVersionIds(state),
        fetched: getVersionComparisonFetchingState(state) !== NOT_ASKED,
        fetching: getVersionComparisonFetchingState(state) === FETCHING,
        comparison: getVersionComparison(state)
    }
};

export function ComparisonEntry({versionCount, comparison, nested = false, title = null}) {
    if (Array.isArray(comparison)) {
        if (!comparison.length) {
            return null;
        }

        const first = comparison[0];

        return (
            <tr>
                <td>
                    <Trans t={first.title} domain="data-object"/> <small className="text-muted">{first.titleAddition}</small>
                </td>

                {comparison.map((item, i) => {
                    if (!item.value || (typeof item.value === "object" && Object.keys(item.value).length === 0)) {
                        return null;
                    }

                    return (
                        <td key={i} className={item.dirty ? null : "text-muted"} style={{background: item.dirty ? "red" : "transparent"}} dangerouslySetInnerHTML={{__html: item.value}}></td>
                    );
                })}
            </tr>
        );
    } else {
        const content = [];

        if (nested && title && title !== LOCALIZED_FIELDS) {
            content.push((
                <tr key="basic-title-for-version-row">
                    <td colSpan={versionCount + 1} className="font-weight-bold">
                        <Trans t={title} domain="data-object"/>
                    </td>
                </tr>
            ));
        }

        Object.entries(comparison).forEach(([key, value]) => {
            content.push((
                <ComparisonEntry key={key} versionCount={versionCount} comparison={value} nested={true} title={key}/>
            ));
        });

        return content;
    }
}

function VersionComparison({fetched, fetching, comparison, versionIds}) {
    return (
        <div>
            {fetching && (<LoadingIndicator/>)}

            {!fetching && fetched && comparison && (
                <Table>
                    <thead>
                    <tr>
                        <th><Trans t="version.attribute" domain="data-object"/></th>
                        {versionIds.map(id => (
                            <th key={id}>{id}</th>
                        ))}
                    </tr>
                    </thead>
                    <tbody>
                    <ComparisonEntry versionCount={versionIds.length} comparison={comparison}/>
                    </tbody>
                </Table>
            )}
        </div>
    );
}

export default connect(mapStateToProps)(VersionComparison);