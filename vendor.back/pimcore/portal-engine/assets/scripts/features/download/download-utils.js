/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {getDownloadAttributeById} from "~portal-engine/scripts/features/selectors";
import {truthy} from "~portal-engine/scripts/utils/utils";

export function downloadSelectionStateToConfig({selectedIds, formatsById, setupsById, dataPoolId, state}) {
    return selectedIds.map(attributeId => {
        const item = getDownloadAttributeById(state, {
            id: attributeId,
            dataPoolId
        });

        if (!item) {
            return null;
        }

        const {type, localized, attribute, formats} = item;

        return {
            type,
            localized,
            attribute,
            format: (formats && formats.length)
                ? (formatsById[attributeId] || formats[0].id)
                : null,
            setup: (setupsById && setupsById[attributeId]) ? setupsById[attributeId] : null
        }
    }).filter(truthy);
}

export function downloadConfigToSelectionState(configs) {
    let selectedIds = configs.map(config => getAttributeKey(config));
    let formatsById = configs.reduce((byId, currentConfig) =>
        currentConfig.format ? {
            ...byId,
            [getAttributeKey(currentConfig)]: currentConfig.format
        } : byId, {}
    );
    let setupsById = configs.reduce((byId, currentConfig) =>
        currentConfig.format ? {
            ...byId,
            [getAttributeKey(currentConfig)]: currentConfig.setup
        } : byId, {}
    );

    return {selectedIds, formatsById, setupsById}
}

function getAttributeKey(config) {
    return config.attribute ? `${config.type}-${config.attribute}` : config.type;
}