/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState, useEffect} from "react";
import {metadataLabel, updateMetadata} from "~portal-engine/scripts/features/asset/asset-layout";
import {getWysiwygToolbarConfig} from "~portal-engine/scripts/features/element/element-layout";
import RichTextEditor from "react-rte";
import FormGroup from "~portal-engine/scripts/components/FormGroup";

export default function ({layout, data, language, extractData, context}) {
    const value = extractData(data, layout.name, language, undefined);
    const [editorValue, setEditorValue] = useState(RichTextEditor.createValueFromString(value == null ? "" : value, "html"));

    useEffect(() => {
        // delegate external clear to editor
        if (value === null) {
            setEditorValue(RichTextEditor.createValueFromString("", "html"))
        }
    }, [value]);

    const handleChange = (value) => {
        setEditorValue(value);
        updateMetadata(layout, context, language, value.toString("html"));
    };

    return (
        <FormGroup label={metadataLabel(layout, context, language, value)} className={"edit-type edit-type--wysiwyg vertical-gutter__item"}>
            <RichTextEditor
                value={editorValue}
                multiline
                variant="filled"
                readOnly={context.readOnly}
                toolbarConfig={getWysiwygToolbarConfig()}
                onChange={handleChange}
            />
        </FormGroup>
    );
}