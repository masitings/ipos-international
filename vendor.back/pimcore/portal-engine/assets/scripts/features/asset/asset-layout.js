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
import {addAdapters, extractLabel} from "~portal-engine/scripts/features/element/element-layout";
import Label from "~portal-engine/scripts/features/asset/components/metadata/edit/Label";
import Clear from "~portal-engine/scripts/features/asset/components/metadata/edit/Clear";
import KeepMetadata from "~portal-engine/scripts/features/asset/components/metadata/edit/KeepMetadata";
import * as adaptersByName from "~portal-engine/scripts/features/asset/render-adapters";
import {ReactComponent as WarningIcon} from "~portal-engine/icons/exclamation-circle";

let _setup = false;

export function setupAssetLayout() {
    if (!_setup) {
        addAdapters(adaptersByName);
    }

    _setup = true;
}

let _layout = {};
let _editableLanguages = [];

export function setLayout(layout) {
    _layout = layout;
}

export function getLayout() {
    return _layout;
}

export function getEditableLanguages() {
    return _editableLanguages;
}

export function setEditableLanguages(languages) {
    _editableLanguages = languages;
}

export function updateMetadata(layout, context, language, value) {
    if (!context || !context.updateMetadata) {
        return;
    }

    context.updateMetadata(layout.name, language, value);
}

export function clearMetadata(layout, context, language) {
    updateMetadata(layout, context, language, null);
}

export function prepareEditableLanguage(context, language) {
    return {
        ...context,
        readOnly: context.readOnly || !getEditableLanguages().includes(language)
    };
}

export function metadataLabel(layout, context, language, value) {
    const label = extractLabel(layout);
    const children = [];

    if(layout.mandatory) {
        children.push((
            <span className="text-muted ml-1">*</span>
        ));
    }

    if (!context.readOnly && context.enableClear) {
        children.push((
            <Clear
                key="clear"
                value={value}
                reset={(value) => updateMetadata(layout, context, language, value)}
                clear={() => clearMetadata(layout, context, language)}
            />
        ));

        children.push((
            <KeepMetadata
                key="keep"
                isKeepingMetadata={() => context.isKeepingMetadata(layout.name)}
                toggleKeepMetadata={() => context.toggleKeepMetadata(layout.name)}
            />
        ));
    }

    if (context.isValid && !context.isValid(layout.name)) {
        children.push((
            <span key="warning" className="text-danger">
                <WarningIcon height={12} className="ml-2"/>
            </span>
        ));
    }

    return (
        <Label label={label} children={children}/>
    );
}