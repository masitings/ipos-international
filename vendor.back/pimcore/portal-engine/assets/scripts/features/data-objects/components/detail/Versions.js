/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState} from "react";
import {connect} from "react-redux";
import Modal from "react-bootstrap/Modal";
import {
    fetchComparisonForSelectedVersions,
    fetchVersions
} from "~portal-engine/scripts/features/data-objects/data-object-actions";
import {
    getVersionHistory,
    getVersionsFetchingState,
    getSelectedVersionIds,
} from "~portal-engine/scripts/features/data-objects/data-object-selectors";
import {FETCHING, NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import Table from "~portal-engine/scripts/components/Table";
import Trans from "~portal-engine/scripts/components/Trans";
import VersionHistoryRow from "~portal-engine/scripts/features/data-objects/components/detail/versions/VersionHistoryRow";
import VersionComparison from "./versions/VersionComparison";

export const mapStateToProps = state => {
    return {
        fetched: getVersionsFetchingState(state) !== NOT_ASKED,
        fetching: getVersionsFetchingState(state) === FETCHING,
        versions: getVersionHistory(state),
        selectedVersionIds:  getSelectedVersionIds(state)
    };
};

export const mapDispatchToProps = dispatch => {
    return {
        fetchVersions: () => {
            dispatch(fetchVersions());
        },

        compareSelectedVersions: () => {
            dispatch(fetchComparisonForSelectedVersions());
        }
    }
};

export function Versions({fetched, fetching, versions, selectedVersionIds, compareSelectedVersions, fetchVersions}) {
    if (!fetched && !fetching) {
        fetchVersions();
    }

    const [isComparing, setIsComparing] = useState(false);

    return (
        <div className="row">
            <div className="col-12 col-md-8 offset-md-2">
                {fetching && (<LoadingIndicator/>)}

                {fetched && versions.length > 0 && (
                    <Table>
                        <thead>
                        <tr>
                            <th><Trans t="id" domain="data-object"/></th>
                            <th><Trans t="note" domain="data-object"/></th>
                            <th><Trans t="date" domain="data-object"/></th>
                            <th className="text-right"><Trans t="compare" domain="data-object"/></th>
                        </tr>
                        </thead>
                        <tbody>
                        {versions.map((version, i) => (
                            <VersionHistoryRow key={i} version={version}/>
                        ))}
                        </tbody>
                    </Table>
                )}

                {fetched && versions.length === 0 && (
                    <div className="text-center mt-5">
                        <h3><Trans t="no-versions-available"/></h3>
                    </div>
                )}

                <Modal scrollable={true} size="xl" show={isComparing} onHide={() => setIsComparing(false)}>
                    <Modal.Header closeButton>
                        <Modal.Title><Trans t="compare-versions" domain="data-object"/></Modal.Title>
                    </Modal.Header>

                    <Modal.Body className="overflow-auto">
                        <VersionComparison/>
                    </Modal.Body>
                </Modal>

                {selectedVersionIds.length > 0 && (
                    <div className="floating-action">
                        <button onClick={() => {
                            setIsComparing(true);
                            compareSelectedVersions()
                        }} className="btn btn-gray btn-shadow">
                            <Trans t="compare-versions" domain="data-object"/> ({selectedVersionIds.length})
                        </button>
                    </div>
                )}
            </div>
        </div>
    );
}

export default connect(mapStateToProps, mapDispatchToProps)(Versions);