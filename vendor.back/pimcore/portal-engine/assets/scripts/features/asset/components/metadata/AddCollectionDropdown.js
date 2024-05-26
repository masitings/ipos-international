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
import {Dropdown} from "react-bootstrap";
import {addMetadata} from "~portal-engine/scripts/features/asset/asset-actions";
import {getMetadataEditDataById, getMetadataLayout} from "~portal-engine/scripts/features/asset/asset-selectors";
import {isRemoved} from "~portal-engine/scripts/features/asset/asset-utils";
import Trans from "~portal-engine/scripts/components/Trans";
import {ReactComponent as PlusIcon} from "~portal-engine/icons/plus";

export const mapStateToProps = (state, {id}) => ({
    metadata: getMetadataEditDataById(state, id),
    metadataLayout: getMetadataLayout(state),
});

export const mapDispatchToProps = (dispatch, {id}) => ({
    addMetadata: (prefix) => dispatch(addMetadata({id, prefix})),
});

export function AddCollectionDropdown({metadata, metadataLayout, addMetadata}) {
    const missingCollections = [];
    const collections = Object.keys(metadataLayout);
    const existingCollections = Object.keys(metadata.metadata || {});

    collections.forEach((key) => {
        if (!existingCollections.includes(key) || isRemoved(metadata, key)) {
            missingCollections.push(key);
        }
    });

    if (!missingCollections.length) {
        return null;
    }

    return (
        <Dropdown>
            <Dropdown.Toggle variant="light" size="sm" className="icon-btn mr-1">
                <PlusIcon height={14}/>
            </Dropdown.Toggle>

            <Dropdown.Menu>
                {missingCollections.map((collection) => (
                    <Dropdown.Item key={collection} onClick={() => addMetadata(collection)}>
                        <Trans t={collection} domain="asset"/>
                    </Dropdown.Item>
                ))}
            </Dropdown.Menu>
        </Dropdown>
    );
}

export default connect(mapStateToProps, mapDispatchToProps)(AddCollectionDropdown);