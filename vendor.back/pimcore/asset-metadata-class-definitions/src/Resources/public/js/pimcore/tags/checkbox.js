/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.tags.checkbox");
pimcore.plugin.asset_metadata_class_definitions.bundle.tags.checkbox = Class.create(pimcore.object.tags.checkbox, {

    registerChangeListener: function(changeContext) {
        if (changeContext) {
            this.changeContext = changeContext;
            this.changeListener = function() {
                if (changeContext.callback) {
                    changeContext.callback(this.changeContext, this.fieldConfig.name, this.fieldConfig.fieldtype,  this.getValue(), this.getAdditionalGridConfig(), this.getTargetId());
                }
            }.bind(this);
            this.checkbox.addListener("change", this.changeListener);
        }
    },

    processMetadataChange: function(eventType, name, language, newValue, type, originator) {
        if (originator != this.getTargetId()) {
            this.setValue(newValue, originator);
        }
    },

    getTargetId: function() {
        return this.component.getId();
    },


    setValue: function (newValue, originator) {

        this.checkbox.setValue(newValue);
    },

    getValue: function () {
        return this.checkbox.getValue();
    },

    getAdditionalGridConfig: function() {
        return null;
    },

    createEmptyButton: function() {
        if (this.getObject()) {
            this.emptyButton = new Ext.Button({
                iconCls: "pimcore_icon_delete",
                cls: 'pimcore_button_transparent',
                tooltip: t("set_to_null"),
                handler: function () {
                    if (this.data !== null) {
                        this.dataChanged = true;
                    }
                    this.checkbox.setValue(false);

                    this.data = null;
                    this.updateStyle();
                }.bind(this),
                style: "margin-left: 10px; filter:grayscale(100%);",
            });
        }
    },

    updateStyle: function(newStyle) {

        if(!this.getObject()) {
            return;
        }

        var cbEl = this.checkbox.el.down('.x-form-checkbox');

        if (cbEl) {
            if (!newStyle) {
                newStyle = this.getStyle();
            }

            cbEl.setStyle('color', newStyle);
        }
    },


});