/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.tags.manyToManyRelation");
pimcore.plugin.asset_metadata_class_definitions.bundle.tags.manyToManyRelation = Class.create(pimcore.object.tags.manyToManyRelation, {
    setValue: function(newValue, originator) {
        if (originator != this.getTargetId()) {
            this.store.setData(newValue);

        }
    },

    processMetadataChange: function(eventType, name, language, newValue, type, originator) {
        if (originator != this.getTargetId()) {
            this.setValue(newValue, originator);
        }
    },

    getAdditionalGridConfig: function() {
        return null;
    },


    registerChangeListener: function(changeContext) {
        if (changeContext) {
            this.changeContext = changeContext;
            this.changeListener = function() {
                if (changeContext.callback) {
                    changeContext.callback(this.changeContext, this.fieldConfig.name, this.fieldConfig.fieldtype,  this.getValue(), this.getAdditionalGridConfig(), this.getTargetId());
                }
            }.bind(this);
            this.store.addListener("update", this.changeListener);
            this.store.addListener("remove", this.changeListener);
            this.store.addListener("add", this.changeListener);
        }
    },

    getTargetId: function() {
        return this.component.getId();
    },
});
