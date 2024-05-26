/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.asset.metadata.tags.multiselect");
pimcore.asset.metadata.tags.multiselect = Class.create(pimcore.asset.metadata.tags.abstract, {

    type: "multiselect",
    initialize: function (data, fieldConfig) {

        this.data = data;
        this.fieldConfig = fieldConfig;
    },

    marshal: function(value) {
        if (Array.isArray(value)) {
            value = implode(",", value);
        }
        return value;
    },

    getGridCellEditor: function (gridtype, record) {
        let options = [];
        if (record.data.config) {
            let config = record.data.config;
            config = config.split(",");
            for (let i = 0; i < config.length; i++) {
                let optionItem = config[i];
                options.push({
                    key:optionItem,
                    value: optionItem
                    }
                );
            }
        }
        return new pimcore.element.helpers.gridCellEditor({
            fieldInfo: {
                layout: {
                    fieldtype: "multiselect",
                    title: record.data.name,
                    options: options
                }
            },
            elementType: "assetmetadata"
        });
    },

    getLayoutEdit: function () {

        // generate store
        var validValues = [];
        var hasHTMLContent = false;
        var storeData = this.prepareStoreDataAndFilterLabels(this.fieldConfig);
        for (var i = 0; i < storeData.length; i++) {
            validValues.push(storeData[i].text);
            if(storeData[i].text.indexOf('<') >= 0) {
                hasHTMLContent = true;
            }
        }

        var store = Ext.create('Ext.data.Store', {
            fields: ['id', 'text'],
            data: storeData,
        });


        var options = {
            name: this.fieldConfig.name,
            triggerAction: "all",
            editable: false,
            fieldLabel: this.fieldConfig.title,
            store: store,
            componentCls: "object_field",
            valueField: 'id',
            labelWidth: this.fieldConfig.labelWidth ? this.fieldConfig.labelWidth : 100,
            listeners: {
                change : function  ( multiselect , newValue , oldValue , eOpts ) {
                    if (this.fieldConfig.maxItems && multiselect.getValue().length > this.fieldConfig.maxItems) {
                        // we need to set a timeout so setValue is applied when change event is totally finished
                        // without this, multiselect wont be updated visually with oldValue (but internal value will be oldValue)
                        setTimeout(function(multiselect, oldValue){
                            multiselect.setValue(oldValue);
                        }, 100, multiselect, oldValue);

                        Ext.Msg.alert(t("error"),t("limit_reached"));
                    }
                    return true;
                }.bind(this)
            }
        };

        if (this.fieldConfig.width) {
            options.width = this.fieldConfig.width;
        } else {
            options.width = 300;
        }

        options.width += options.labelWidth;

        if (this.fieldConfig.height) {
            options.height = this.fieldConfig.height;
        } else if (this.fieldConfig.renderType != "tags") {
            options.height = 100;
        }

        if (typeof this.data == "string" || typeof this.data == "number") {
            options.value = this.data;
        }

        if (this.fieldConfig.renderType == "tags") {
            options.queryMode = 'local';
            options.editable = true;
            if(hasHTMLContent) {
                options.labelTpl = '{[Ext.util.Format.stripTags(values.text)]}';
            }
            this.component = Ext.create('Ext.form.field.Tag', options);
        } else {
            this.component = Ext.create('Ext.ux.form.MultiSelect', options);
        }

        return this.component;
    },

    prepareStoreDataAndFilterLabels: function(fieldConfig) {

        var storeData = [];
        var restrictTo = null;

        if (fieldConfig.restrictTo) {
            restrictTo = fieldConfig.restrictTo.split(",");
        }

        if (fieldConfig.options) {
            for (var i = 0; i < fieldConfig.options.length; i++) {
                var value = fieldConfig.options[i].value;
                if (restrictTo) {
                    if (!in_array(value, restrictTo)) {
                        continue;
                    }
                }

                var label = t(fieldConfig.options[i].key);
                if(label.indexOf('<') >= 0) {
                    label = replace_html_event_attributes(strip_tags(label, "div,span,b,strong,em,i,small,sup,sub2"));
                }
                storeData.push({id: value, text: label});
            }
        }

        return storeData;

    },

    getCellEditValue: function () {
        let val = this.component.getValue();
        val = implode("," , val);
        return val;
    },

    getGridColumnConfig: function (field) {
        return {
            text: field.label,
            width: this.getColumnWidth(field, 300),
            sortable: false,
            dataIndex: field.key,
            getEditor: this.getListfolderEditor.bind(this, field),
            renderer: this.getRenderer(field)
        };
    },

    getListfolderEditor: function (field) {
        let options = [];
        if (field.layout.config) {
            let config = field.layout.config;
            config = config.split(",");
            for (let i = 0; i < config.length; i++) {
                let optionItem = config[i];
                options.push({
                        key:optionItem,
                        value: optionItem
                    }
                );
            }
        }
        return new pimcore.element.helpers.gridCellEditor({
            fieldInfo: {
                layout: {
                    fieldtype: "multiselect",
                    title: field.key,
                    options: options
                }
            },
            elementType: "assetmetadata"
        });
    },

});
