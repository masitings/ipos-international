/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.edit");
pimcore.plugin.asset_metadata_class_definitions.bundle.edit = {

    registerChangeListener: function(changeContext) {
        if (changeContext) {
            this.changeContext = changeContext;
            this.changeListener = function() {
                if (changeContext.callback) {
                    changeContext.callback(this.changeContext, this.fieldConfig.name, this.fieldConfig.fieldtype,  this.getValue(), this.getAdditionalGridConfig(), this.getTargetId());
                }
            }.bind(this);
            this.component.addListener("change", this.changeListener);
        }
    },

    processMetadataChange: function(eventType, name, language, newValue, type, originator) {
        if (originator != this.getTargetId()) {
            this.setValue(newValue, originator);
        }
    },

    getTargetId: function() {
        return this.component.getId();
    }
};