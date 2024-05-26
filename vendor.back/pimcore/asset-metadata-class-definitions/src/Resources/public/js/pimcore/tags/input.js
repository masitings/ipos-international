/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.tags.input");
pimcore.plugin.asset_metadata_class_definitions.bundle.tags.input = Class.create(pimcore.object.tags.input, {
    setValue: function(newValue) {
        this.component.setValue(newValue);
    },

    getValue: function() {
        return this.component.getValue();
    },

    getAdditionalGridConfig: function() {
        return null;
    }
});

pimcore.plugin.asset_metadata_class_definitions.bundle.tags.input.addMethods(pimcore.plugin.asset_metadata_class_definitions.bundle.edit);