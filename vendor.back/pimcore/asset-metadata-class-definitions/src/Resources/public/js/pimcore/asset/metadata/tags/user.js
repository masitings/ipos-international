/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.asset.metadata.tags.user");
pimcore.asset.metadata.tags.user = Class.create(pimcore.asset.metadata.tags.select, {

    type: "user",

    getGridCellEditor: function (gridtype, record) {
        let config = record.data.config;
        config = Ext.decode(config);

        var options = config["options"];

        var store = new Ext.data.Store({
            autoDestroy: true,
            fields: ['key', 'value'],
            data: options
        });
        var editorConfig = {};

        if (config.width) {
            if (intval(config.width) > 10) {
                editorConfig.width = config.width;
            }
        }

        editorConfig = Object.assign(editorConfig, {
            store: store,
            triggerAction: "all",
            editable: false,
            mode: "local",
            valueField: 'value',
            displayField: 'key',
            displayTpl: Ext.create('Ext.XTemplate',
                '<tpl for=".">',
                '{[Ext.util.Format.stripTags(values.key)]}',
                '</tpl>'
            )
        });

        return new Ext.form.ComboBox(editorConfig);
    },

    getGridCellRenderer: function(value, metaData, record, rowIndex, colIndex, store) {

        let config = record.data.config;
        config = Ext.decode(config);
        if (config) {
            let options = config["options"];
            if (options) {
                for (let i = 0; i < options.length; i++) {
                    let option = options[i];
                    if (option["value"] == value) {
                        return option["key"];
                    }
                }
            }
        }

        return value;
    },

    getGridColumnConfig:function (field) {
        return {
            text: field.label,
            editable: false,
            width: this.getColumnWidth(field, 80),
            sortable: false,
            dataIndex: field.key,
            getEditor: this.getListfolderEditor.bind(this, field),
            renderer: function (key, value, metaData, record) {
                if (value) {
                    let optionsKey = key + "%options";
                    if (record.data[optionsKey]) {
                        let options = record.data[optionsKey];
                        for (let i = 0; i < options.length; i++) {
                            let option = options[i];
                            if (option["value"] == value) {
                                return option["key"];
                            }
                        }

                    }
                }
                return value;
            }.bind(this, field.key)
        };
    },

    addGridOptionsFromColumnConfig: function (key, v, rec) {
        if (v && typeof v.options !== "undefined") {
            // split it up and store the options in a separate field
            rec.set(key + "%options", v.options, {convert: false, dirty: false});
            return v.value;
        }
        return v;
    },

    getListfolderEditor: function (field) {
        var config = field.layout.config;
        config = Ext.decode(field.layout.config);
        let options = config["options"];
        var store = new Ext.data.Store({
            autoDestroy: true,
            fields: ['key', 'value'],
            data: options
        });
        var editorConfig = {};

        if (config.width) {
            if (intval(config.width) > 10) {
                editorConfig.width = config.width;
            }
        }

        editorConfig = Object.assign(editorConfig, {
            store: store,
            triggerAction: "all",
            editable: false,
            mode: "local",
            valueField: 'value',
            displayField: 'key',
            displayTpl: Ext.create('Ext.XTemplate',
                '<tpl for=".">',
                '{[Ext.util.Format.stripTags(values.key)]}',
                '</tpl>'
            )
        });

        return new Ext.form.ComboBox(editorConfig);
    },

    getLayoutEdit: function () {
        var store = new Ext.data.Store({
            autoDestroy: true,
            fields: ['key', 'value'],
            data: this.fieldConfig.options
        });

        var options = {
            name: this.fieldConfig.name,
            triggerAction: "all",
            editable: true,
            queryMode: 'local',
            autoComplete: false,
            forceSelection: true,
            selectOnFocus: true,
            fieldLabel: this.fieldConfig.title,
            store: store,
            componentCls: "object_field",
            width: 250,
            displayField: 'key',
            valueField: 'value',
            labelWidth: 100
        };

        this.component = new Ext.form.ComboBox(options);

        return this.component;
    },

    previewRenderer: function(value, record) {
        if (value) {

            let options = value.options;
            value = value.value;

            for (let i = 0; i < options.length; i++) {
                let option = options[i];
                if (option["value"] == value) {
                    return option["key"];
                }
            }

            return value;
        }
    },

    prepareBatchEditLayout: function(layout) {
        let config = layout["config"];
        config = Ext.decode(config);
        let options = config["options"];
        layout["options"] = options;

        return layout;
    }

});
