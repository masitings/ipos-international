/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.asset.metadata.tags.calculatedValue");
pimcore.asset.metadata.tags.calculatedValue = Class.create(pimcore.asset.metadata.tags.abstract, {

    type: "calculatedValue",

    initialize: function (data, fieldConfig) {

        this.data = data;
        this.fieldConfig = fieldConfig;
    },

    getGridCellEditor: function (gridtype, record) {
        return null;
    },


    getGridColumnConfig: function (field) {
        return {
            text: field.label,
            width: this.getColumnWidth(field, 300),
            sortable: false,
            dataIndex: field.key,
            getEditor: this.getListfolderEditor.bind(this, field),
            filter: 'string',
            renderer: this.getRenderer(field)
        };
    },

    getListfolderEditor: function (field) {
        return null;
    }
});
