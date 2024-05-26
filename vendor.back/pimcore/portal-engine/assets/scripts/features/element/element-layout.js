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
import {convertCssToObject} from "~portal-engine/scripts/utils/data-object-data";
import {basicRenderValue} from "~portal-engine/scripts/features/data-objects/components/detail/BasicValueRow";
import Trans from "~portal-engine/scripts/components/Trans";
import {LOCALIZED_FIELDS} from "~portal-engine/scripts/consts/layout";
import {getAttributeId} from "~portal-engine/scripts/features/download/download-reducer";

let _validLanguages = [];
let _languageConfig = {};
let _adapters = {};

/**
 * @param languages
 */
export function setValidLanguages(languages) {
    _validLanguages = languages;
}

/**
 * @returns {[]}
 */
export function getValidLanguages() {
    return _validLanguages;
}

export function setLanguageConfig(config) {
    _languageConfig = config;
}

export function getLanguageConfig(language) {
    return _languageConfig[language];
}

/**
 * @param {{}} adaptersByName
 */
export function addAdapters(adaptersByName) {
    _adapters = {
        ..._adapters,
        ...adaptersByName
    };
}

/**
 * @param fieldtype
 */
function getLayoutRenderAdapter(fieldtype) {
    return _adapters[fieldtype] || _adapters["defaultLayout"];
}

/**
 * @param fieldtype
 */
function getDataRenderAdapter(fieldtype) {
    return _adapters[fieldtype] || _adapters["defaultData"];
}

/**
 * @param layout
 * @param data
 * @param language
 * @param extractData
 * @param renderValue
 * @param key
 * @param className
 * @param context
 *
 * @returns {array|string}
 */
export function renderLayout(
    layout,
    data,
    language = null,
    extractData = basicExtractData,
    renderValue = basicRenderValue,
    className = '',
    key = null,
    context = {}
) {
    // not a valid layout definition object
    if (typeof layout !== "object") {
        return null;
    }

    let Adapter = null;

    if (typeof layout.adapter === "function") {
        // layout functions can be injected, just call them to retrieve their content
        Adapter = layout.adapter;
    } else {
        // basic pimcore layout definitions
        if (layout.datatype === "layout") {
            Adapter = getLayoutRenderAdapter(layout.fieldtype);
        } else {
            Adapter = getDataRenderAdapter(layout.fieldtype);
        }

        if (!Adapter) {
            return null;
        }

        Adapter = Adapter["default"];
    }

    if (!key) {
        key = extractKey(layout);
    }

    return (
        <Adapter
            key={key}
            layout={layout}
            data={data}
            language={language}
            extractData={extractData}
            renderValue={renderValue}
            className={className}
            context={context}
        />
    );
}

/**
 * @param layout
 * @param data
 * @param language
 * @param extractData
 * @param renderValue
 * @param childrenClassName
 * @param context
 *
 * @returns {array|string}
 */
export function renderChildren(
    layout,
    data,
    language = null,
    extractData = basicExtractData,
    renderValue = basicRenderValue,
    childrenClassName = '',
    context = {}
) {
    return extractChildren(layout).map((child, i) => {
        return renderLayout(child, data, language, extractData, renderValue, childrenClassName, i, context);
    });
}

/*
 * @param layout
 * @returns {[]}
 */
export function extractChildren(layout) {
    if (!layout.childs) {
        return [];
    }

    return layout.childs;
}

/**
 * @param layout
 * @param children
 */
export function setChildren(layout, children) {
    layout.childs = children;
}

/**
 * @param layout
 * @param key
 * @param title
 * @param adapter
 */
export function addChild(layout, key, title, adapter) {
    layout.childs.push({
        name: key,
        title: title,
        adapter: adapter
    });
}

/**
 * @param context
 *
 * @returns {boolean}
 */
export function isInsideTab(context) {
    return !!extractContext(context, "tab");
}

/**
 * @param context
 */
export function prepareChildAsTab(context) {
    return addContext(context, "tab", true);
}

/**
 * @param context
 * @param key
 * @param data
 */
export function addContext(context, key, data) {
    if (typeof context !== "object") {
        return context;
    }

    return {
        ...context,
        [key]: data
    }
}

/**
 * @param context
 * @param key
 *
 * @returns {null|*}
 */
export function extractContext(context, key) {
    if (typeof context !== "object") {
        return null;
    }

    return context[key];
}

/**
 * @param layout
 *
 * @returns {string}
 */
export function extractKey(layout) {
    return layout.name;
}

/**
 * @param layout
 * @param style
 *
 * @return {object}
 */
export function extractStyle(layout, style = {}) {
    let layoutStyle = layout.bodyStyle || layout.style || "";

    layoutStyle = convertCssToObject(layoutStyle);

    return {...layoutStyle, ...style};
}

/**
 * @param layout
 *
 * @returns {string}
 */
export function extractLabel(layout) {
    if (layout.title) {
        return (
            <Trans t={layout.title} domain="data-object"/>
        )
    }

    return null;
}

/**
 * @param layout
 * @param label
 */
export function setLabel(layout, label) {
    layout.title = label;
}

/**
 * @param layout
 * @returns {string|null}
 */
export function extractRegion(layout) {
    return layout.region || null;
}

/**
 * @param layout
 * @returns {int|null}
 */
export function extractWidth(layout) {
    return layout.width || 0;
}

/**
 * @param layout
 *
 * @returns {object}
 */
export function removeLabel(layout) {
    return {
        ...layout,
        title: null
    };
}

/**
 *
 * @param data
 * @param name
 * @param language
 * @param defaultValue
 *
 * @returns {*}
 */
export function basicExtractData(data, name, language = null, defaultValue) {
    if (language) {
        data = data[LOCALIZED_FIELDS];
    }

    if (typeof data !== "object" || data[name] === undefined) {
        return defaultValue;
    }

    let extracted = data[name];

    if (extracted && language) {
        if (typeof extracted !== "object" || extracted[language] === undefined) {
            return defaultValue;
        }

        extracted = extracted[language];
    }

    return extracted !== undefined ? extracted : defaultValue;
}

const _toolbarConfig = {
    // see @https://github.com/sstur/react-rte/blob/master/src/lib/EditorToolbarConfig.js
    display: ['INLINE_STYLE_BUTTONS', 'BLOCK_TYPE_BUTTONS', 'BLOCK_TYPE_DROPDOWN', 'HISTORY_BUTTONS'],
    INLINE_STYLE_BUTTONS: [
        {label: 'Bold', style: 'BOLD'},
        {label: 'Italic', style: 'ITALIC'},
        {label: 'Underline', style: 'UNDERLINE'}
    ],
    BLOCK_TYPE_DROPDOWN: [
        {label: 'Normal', style: 'unstyled'},
        {label: 'h1', style: 'header-one'},
        {label: 'h2', style: 'header-two'},
        {label: 'h3', style: 'header-three'}
    ],
    BLOCK_TYPE_BUTTONS: [
        {label: 'UL', style: 'unordered-list-item'},
        {label: 'OL', style: 'ordered-list-item'}
    ]
};

export function getWysiwygToolbarConfig() {
    return _toolbarConfig;
}

export function transformSelectOptions(options) {
    if (!Array.isArray(options)) {
        return [];
    }

    return options.map((option) => ({
        value: option.value,
        label: option.key || options.label
    }));
}

export function extractDownloadTypeAttributes(layout) {
    if (!layout.portalDownloadTypes) {
        return [];
    }

    return layout.portalDownloadTypes.map(getAttributeId);
}