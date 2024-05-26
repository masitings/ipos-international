/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from 'react';
import {connect} from "react-redux";
import DownloadAttribute from "~portal-engine/scripts/features/download/components/DownloadConfig/DownloadAttribute";
import {
    getDownloadAttributeIdsByDataPoolId,
    getDownloadConfigFetchStateByDataPoolId,
    getDownloadConfigModalMode
} from "~portal-engine/scripts/features/selectors";
import {getConfigModalAttributes} from "~portal-engine/scripts/features/download/download-selectors";
import {noop} from "~portal-engine/scripts/utils/utils";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import {FETCHING, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import {MODAL_MODES} from "~portal-engine/scripts/features/download/dowload-consts";
import {getMultiDownloadFetchingState} from "~portal-engine/scripts/features/download/download-selectors";

export const mapStateToProps = (state, {dataPoolId}) => {
    let fetchingState = getDownloadConfigFetchStateByDataPoolId(state, dataPoolId);
    let mode = getDownloadConfigModalMode(state);
    const attributes = getConfigModalAttributes(state.download);

    return {
        isLoading: fetchingState !== SUCCESS
            || (mode === MODAL_MODES.DOWNLOAD && getMultiDownloadFetchingState(state.download) === FETCHING),
        attributeIds: attributes && attributes.length ? attributes : getDownloadAttributeIdsByDataPoolId(state, dataPoolId)
    }
};

export function AttributeList(props) {
    const {
        attributeIds = [],
        isLoading = false,
        selections,
        dataPoolId,
        allowEmptySelection = false,
        filterAttributeOptions = () => true,
        onChangeSelections = noop
    } = props;

    const selectionChangedHandler = id => {
        onChangeSelections({
            ...selections,
            selectedIds: selections.selectedIds.includes(id)
                ? selections.selectedIds.filter(currentId => currentId !== id)
                : [...selections.selectedIds, id]
        });
    };

    const formatChangedHandler = (id, value) => {
        onChangeSelections({
            ...selections,
            formatsById: {
                ...selections.formatsById,
                [id]: value
            }
        })
    };

    const setupChangedHandler = (id, key, value) => {
        onChangeSelections({
            ...selections,
            setupsById: {
                ...selections.setupsById,
                [id]: {
                    ...selections.setupsById?.[id],
                    [key]: value
                }
            }
        })
    };

    return (
        isLoading ? (
            <LoadingIndicator className="my-4"/>
        ) : (
            <ul className="list-unstyled mb-0 vertical-gutter--3">
                {attributeIds.map(id => (
                    <li key={id} className="vertical-gutter__item">
                        <DownloadAttribute
                            filterOptions={option => filterAttributeOptions(id, option)}
                            key={id}
                            attributeId={id}
                            dataPoolId={dataPoolId}
                            isSelected={selections.selectedIds.includes(id) || (!allowEmptySelection && attributeIds.length === 1)}
                            selectedFormat={selections.formatsById[id]}
                            setup={selections.setupsById[id]}
                            onSelectionChanged={() => selectionChangedHandler(id)}
                            onFormatChanged={({value}) => formatChangedHandler(id, value)}
                            onSetupChanged={(key, value) => setupChangedHandler(id, key, value)}
                        />
                    </li>
                ))}
            </ul>
        )
    )
}

export default connect(mapStateToProps)(AttributeList);