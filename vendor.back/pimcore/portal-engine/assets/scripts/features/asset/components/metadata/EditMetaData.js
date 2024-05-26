/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment, useEffect, useState} from "react";
import {connect} from "react-redux";
import {
    getMetadataEditDataById,
    getMetadataLayout,
    getMetadataLayoutError,
    getMetadataLayoutFetchingState,
    hasMetadataEditById
} from "~portal-engine/scripts/features/asset/asset-selectors";
import {editMetadata, fetchMetadataLayout, updateMetadata, removeMetadata, saveMetadata, toggleKeepMetadata} from "~portal-engine/scripts/features/asset/asset-actions";
import {showError} from "~portal-engine/scripts/utils/general";
import {FAILED, NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import Tabs from "~portal-engine/scripts/components/tab/Tabs";
import Tab from "~portal-engine/scripts/components/tab/Tab";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import {
    basicExtractData,
    renderLayout,
    prepareChildAsTab
} from "~portal-engine/scripts/features/element/element-layout";
import {basicRenderValue} from "~portal-engine/scripts/features/data-objects/components/detail/BasicValueRow";
import AddCollectionDropdown from "~portal-engine/scripts/features/asset/components/metadata/AddCollectionDropdown";
import Trans from "~portal-engine/scripts/components/Trans";
import {setupAssetLayout} from "~portal-engine/scripts/features/asset/asset-layout";
import NavigationWithAddition from "~portal-engine/scripts/components/tab/NavigationWithAddition";
import {filterRemoved} from "~portal-engine/scripts/features/asset/asset-utils";
import {ReactComponent as SaveIcon} from "~portal-engine/icons/save";
import {ReactComponent as WarningIcon} from "~portal-engine/icons/exclamation-circle";

setupAssetLayout();

export const mapStateToProps = (state, {id}) => ({
    metadata: getMetadataEditDataById(state, id),
    hasMetadataEdit: hasMetadataEditById(state, id),
    metadataLayout: getMetadataLayout(state),
    metadataLayoutFetchingState: getMetadataLayoutFetchingState(state),
    metadataLayoutError: getMetadataLayoutError(state)
});

export const mapDispatchToProps = (dispatch, {id, setupMetadata = {}}) => ({
    fetchMetadataLayout: () => dispatch(fetchMetadataLayout()),
    editMetadata: () => dispatch(editMetadata({id, metadata: setupMetadata})),
    removeMetadata: (prefix) => dispatch(removeMetadata({id, prefix})),
    saveMetadata: () => dispatch(saveMetadata({id})),
    toggleKeepMetadata: (attribute) => dispatch(toggleKeepMetadata({id, attribute})),
    updateMetadata: (prefix, attribute, language, value) => dispatch(updateMetadata({
        id,
        prefix,
        attribute,
        language,
        value
    }))
});

export function EditMetaData({id, forceMetadata = false, readOnly = false, enableClear = false, saveMetadata, saveCallback = () => {}, hasMetadataEdit, metadataLayout, metadataLayoutFetchingState, metadataLayoutError, fetchMetadataLayout, editMetadata, updateMetadata, metadata, removeMetadata, toggleKeepMetadata}) {
    const isAsset = typeof id === "number";
    const [saving, setSaving] = useState(false);
    const [loaded, setLoaded] = useState(false);

    useEffect(() => {
        if (metadataLayoutFetchingState === NOT_ASKED) {
            fetchMetadataLayout();
        }

        if (!hasMetadataEdit || forceMetadata) {
            // prefill the state with the given metadata
            editMetadata();
        }

        setLoaded(true);
    }, []);

    if (metadataLayoutFetchingState !== SUCCESS || !loaded) {
        if (metadataLayoutFetchingState === FAILED) {
            showError(metadataLayoutError);
        }

        return (
            <LoadingIndicator className="my-4"/>
        );
    }

    let tabs = [(
        <Tab key="empty" label={<Trans t="no-metadata-yet" domain="asset"/>} tab="empty">
            <h4 className="text-center my-4">
                <Trans t="add-new-metadata" domain="asset"/>
            </h4>
        </Tab>
    )];

    if (metadata && metadata.metadata) {
        const entries = Object.entries(metadata.metadata).filter(filterRemoved(metadata));
        const invalidPrefixes = metadata.validation.invalidPrefixes;

        if(entries.length) {
            tabs = entries.map(([key, data]) => {
                let label = key;

                if (invalidPrefixes.includes(key)) {
                    label = (
                        <span>
                            {label}
                            <span className="text-danger">
                                <WarningIcon className="icon-in-text ml-2" height={12}/>
                            </span>
                        </span>
                    );
                }

                const onClose = readOnly ? null : () => removeMetadata(key);

                return (
                    <Tab label={label} key={key} tab={key} onClose={onClose}>
                        {renderLayout(metadataLayout[key], data, null, basicExtractData, basicRenderValue, null, null, prepareChildAsTab({
                            updateMetadata(attribute, language, value) {
                                // add prefix automatically
                                updateMetadata(key, attribute, language, value);
                            },
                            isValid(attribute) {
                                return metadata.validation.invalidFields.filter(field => field.prefix === key && field.name === attribute).length === 0;
                            },
                            toggleKeepMetadata(attribute) {
                                toggleKeepMetadata(key + "." + attribute);
                            },
                            isKeepingMetadata(attribute) {
                                return metadata.meta.keep && metadata.meta.keep[key + "." + attribute];
                            },
                            enableClear: enableClear,
                            readOnly: readOnly
                        }))}
                    </Tab>
                )
            });
        }
    }

    let addition = null;

    if(!readOnly) {
        addition = (
            <div className="d-flex align-items-center">
                <AddCollectionDropdown id={id}/>

                {isAsset ? (
                    <button type="button" className="btn btn-sm btn-primary btn-rounded btn-with-addon text-nowrap" onClick={() => {
                        setSaving(true);
                        saveMetadata(id).then(saveCallback).catch(showError).finally(() => setSaving(false));
                    }}>
                        {saving ?
                            (<Fragment>
                                <span className="btn__addon">
                                    <LoadingIndicator size="inline" showText={false}/>
                                </span>
                                <Trans t="saving-metadata" domain="asset"/>
                            </Fragment>)

                            :

                            (<Fragment>
                                <span className="btn__addon">
                                    <SaveIcon height={14}/>
                                </span>
                                <Trans t="save-metadata" domain="asset"/>
                            </Fragment>)
                        }
                    </button>
                ) : null}
            </div>
        );
    }

    return (
        <Fragment>
            <Tabs addition={addition} classNames={{wrapper: "flex-grow-1"}} Navigation={NavigationWithAddition}>
                {tabs}
            </Tabs>
        </Fragment>
    )
}

export default connect(mapStateToProps, mapDispatchToProps)(EditMetaData);