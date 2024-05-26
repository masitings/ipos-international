/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.tags.country");
pimcore.plugin.asset_metadata_class_definitions.bundle.tags.country = Class.create(pimcore.object.tags.country, {
    setValue: function(newValue) {
        this.component.setValue(newValue);
    },

    getValue: function() {
        return this.component.getValue();
    },

    getAdditionalGridConfig: function() {

        if (this.fieldConfig.options) {
            let result = [];
            for (let i = 0; i < this.fieldConfig.options.length; i++) {
                let option = this.fieldConfig.options[i];
                result.push(option.value);
            }
            result = implode("," , result);
            return result;
        }
    }
});

pimcore.plugin.asset_metadata_class_definitions.bundle.tags.country.addMethods(pimcore.plugin.asset_metadata_class_definitions.bundle.edit);