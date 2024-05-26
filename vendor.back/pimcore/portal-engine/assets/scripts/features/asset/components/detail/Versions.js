/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useEffect, useState} from "react";
import {connect} from "react-redux";
import {getVersionHistoryFetchingState, getVersionHistory, getSelectedVersionIds, getPermissions} from "~portal-engine/scripts/features/asset/asset-selectors";
import {fetchVersionHistory, fetchComparisonForSelectedVersions} from "~portal-engine/scripts/features/asset/asset-actions";
import {NOT_ASKED, FETCHING} from "~portal-engine/scripts/consts/fetchingStates";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import Table from "~portal-engine/scripts/components/Table";
import Trans from "~portal-engine/scripts/components/Trans";
import Modal from "react-bootstrap/Modal";
import VersionHistoryRow from "~portal-engine/scripts/features/asset/components/detail/versions/VersionHistoryRow";
import VersionComparison from "~portal-engine/scripts/features/asset/components/detail/versions/VersionComparison";

export const mapStateToProps = (state) => ({
    fetchingState: getVersionHistoryFetchingState(state),
    versions: getVersionHistory(state),
    selectedVersionIds:  getSelectedVersionIds(state),
    permissions: getPermissions(state)
});

export const mapDispatchToProps = (dispatch) => ({
    fetchVersionHistory: () => dispatch(fetchVersionHistory()),

    compareSelectedVersions: () => {
        dispatch(fetchComparisonForSelectedVersions());
    }
});

export function Versions({fetchingState, fetchVersionHistory, versions, selectedVersionIds, compareSelectedVersions, permissions}) {
    const [isComparing, setIsComparing] = useState(false);
    const allowPublish = permissions && permissions.edit;

    useEffect(() => {
        if (fetchingState === NOT_ASKED) {
            fetchVersionHistory();
        }
    }, []);

    if (fetchingState === NOT_ASKED || fetchingState === FETCHING) {
        return (
            <LoadingIndicator showText={false}/>
        );
    }

    return (
        <div className="row">
            <div className="col-12 col-md-8 offset-md-2">
                {versions.length === 0 ?
                    (
                        <div className="text-center mt-5">
                            <h3><Trans t="no-versions-available"/></h3>
                        </div>

                    )
                    :
                    (
                        <Table>
                            <thead>
                            <tr>
                                <th><Trans t="id" domain="asset"/></th>
                                <th><Trans t="note" domain="asset"/></th>
                                <th><Trans t="date" domain="asset"/></th>
                                <th className="text-center"><Trans t="public" domain="asset"/></th>
                                {allowPublish &&
                                    <th className="text-center"><Trans t="publish" domain="asset"/></th>
                                }
                                <th className="text-center"><Trans t="compare" domain="asset"/></th>
                            </tr>
                            </thead>
                            <tbody>
                            {versions.map((version, i) => (
                                <VersionHistoryRow key={i} version={version} allowPublish={allowPublish}/>
                            ))}
                            </tbody>
                        </Table>
                    )
                }

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