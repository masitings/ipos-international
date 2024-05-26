/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.tags.numeric");
pimcore.plugin.asset_metadata_class_definitions.bundle.tags.numeric = Class.create(pimcore.object.tags.numeric, {
    setValue: function(newValue) {
        this.component.setValue(newValue);
    },

    getValue: function() {
        return this.component.getValue();
    },

    // used to transfer the field config to the grid
    getAdditionalGridConfig: function() {
        return Ext.encode(this.fieldConfig);
    }
});

pimcore.plugin.asset_metadata_class_definitions.bundle.tags.numeric.addMethods(pimcore.plugin.asset_metadata_class_definitions.bundle.edit);