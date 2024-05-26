/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.tags.datetime");
pimcore.plugin.asset_metadata_class_definitions.bundle.tags.datetime = Class.create(pimcore.object.tags.datetime, {
    setValue: function(newValue, originator) {
        if (originator != this.getTargetId()) {
            var tmpDate = new Date(intval(newValue));
            this.datefield.setValue(tmpDate);
            this.timefield.setValue(tmpDate);
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
            this.datefield.addListener("change", this.changeListener);
            this.timefield.addListener("change", this.changeListener);
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

    getLayoutEdit:function () {

        var date = {
            width:130,
            format: "Y-m-d"
        };

        var time = {
            format:"H:i",
            emptyText:"",
            width:90
        };

        if (this.data) {
            var tmpDate = new Date(intval(this.data));
            date.value = tmpDate;
            time.value = tmpDate;
        }

        this.datefield = Ext.create('Ext.form.field.Date', date);
        this.timefield = Ext.create('Ext.form.field.Time', time);

        this.component = Ext.create('Ext.form.FieldContainer', {
            layout: 'hbox',
            fieldLabel:this.fieldConfig.title,
            combineErrors:false,
            items:[this.datefield, this.timefield],
            componentCls:"object_field",
            isDirty: function() {
                return this.datefield.isDirty() || this.timefield.isDirty()
            }.bind(this)
        });

        return this.component;
    },


});