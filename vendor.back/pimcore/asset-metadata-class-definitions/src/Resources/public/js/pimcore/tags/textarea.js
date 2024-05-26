/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.tags.textarea");
pimcore.plugin.asset_metadata_class_definitions.bundle.tags.textarea = Class.create(pimcore.object.tags.textarea, {
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

pimcore.plugin.asset_metadata_class_definitions.bundle.tags.textarea.addMethods(pimcore.plugin.asset_metadata_class_definitions.bundle.edit);