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
import {
    extractStyle,
    getValidLanguages,
    prepareChildAsTab,
    renderChildren,
    getLanguageConfig,
} from "~portal-engine/scripts/features/element/element-layout";
import Tabs from "~portal-engine/scripts/components/tab/Tabs";
import Tab from "~portal-engine/scripts/components/tab/Tab";

export default function ({layout, data, extractData, renderValue, className = '', context = {}}) {
    return (
        <Tabs style={extractStyle(layout)} classNames={{container: `data-type data-type--localizedfields ${className}`, content: 'vertical-gutter'}}>
            {getValidLanguages().map((language) => {
                const languageConfig = getLanguageConfig(language);
                const languageName = languageConfig ? languageConfig.name : language;
                const icon = languageConfig ? languageConfig.icon : null;

                return (
                    <Tab tab={language} key={language} label={languageName} classNames={{container: className, wrapper: 'dt-localized-fields'}} icon={(<img src={icon} className="img-fluid"/>)}>
                        {renderChildren(layout, data, language, extractData, renderValue, className, prepareChildAsTab(context))}
                    </Tab>
                )
            })}
        </Tabs>
    );
}