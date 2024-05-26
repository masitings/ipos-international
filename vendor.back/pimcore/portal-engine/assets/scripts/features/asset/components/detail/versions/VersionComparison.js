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
} from "~portal-engine/scripts/features/asset/asset-selectors";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import Trans from "~portal-engine/scripts/components/Trans";
import Table from "~portal-engine/scripts/components/Table";

export const mapStateToProps = state => {
    return {
        versionIds: getSelectedVersionIds(state),
        fetched: getVersionComparisonFetchingState(state) !== NOT_ASKED,
        fetching: getVersionComparisonFetchingState(state) === FETCHING,
        comparison: getVersionComparison(state)
    }
};

export function VersionComparison({fetched, fetching, comparison, versionIds}) {
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
                        {Object.entries(comparison).map(([attribute, row]) => {
                            if(attribute === "_preview") {
                                return (
                                    <tr key={attribute}>
                                        <td></td>

                                        {versionIds.map((versionId) => (
                                            <td key={versionId} dangerouslySetInnerHTML={{__html: row[versionId]}}></td>
                                        ))}
                                    </tr>
                                );
                            }

                            return (
                                <tr key={attribute}>
                                    <td className="text-nowrap pr-4">
                                        {row.name} <small>{row.language}</small>
                                    </td>

                                    {versionIds.map((versionId) => {
                                        const data = row.data[versionId];

                                        return (
                                            <td key={versionId} style={{background: data.dirty ? "red" : "transparent"}} className={data.dirty ? "text-white" : "text-muted"}>
                                                {data.displayValue}
                                            </td>
                                        );
                                    })}
                                </tr>
                            );
                        })}
                    </tbody>
                </Table>
            )}
        </div>
    );
}

export default connect(mapStateToProps)(VersionComparison);