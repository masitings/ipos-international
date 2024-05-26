/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.asset.metadata.tags.numeric");
pimcore.asset.metadata.tags.numeric = Class.create(pimcore.asset.metadata.tags.abstract, {

    type: "numeric",

    initialize: function (data, fieldConfig) {

        this.data = data;
        this.fieldConfig = fieldConfig;
    },

    marshal: function(value) {
        return value;
    },

    getGridCellEditor: function (gridtype, record) {
        // this is for the editmode
        if (record.data.config) {
            let config = Ext.decode(record.data.config);
            return this.getNumberGridEditor(config);

        }
    },

    getNumberGridEditor: function(config) {
        var editorConfig = {};

        var decimalPrecision = 20;


        if (config.width) {
            if (intval(config.width) > 10) {
                editorConfig.width = config.width;
            }
        }


        if (config["unsigned"]) {
            editorConfig.minValue = 0;
        }

        if (is_numeric(config["minValue"])) {
            editorConfig.minValue = config.minValue;
        }

        if (is_numeric(config["maxValue"])) {
            editorConfig.maxValue = config.maxValue;
        }

        if (config["integer"]) {
            editorConfig.decimalPrecision = 0;
        } else {
            editorConfig.decimalPrecision = 20;
        }
        // we have to use Number since the spinner trigger don't work in grid -> seems to be a bug of Ext
        return new Ext.form.field.Number(editorConfig);
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
            let config = Ext.decode(field.layout.config);
            return this.getNumberGridEditor(config);
        }
    },

});
