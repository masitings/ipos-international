/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.asset.metadata.tags.datetime");
pimcore.asset.metadata.tags.datetime = Class.create(pimcore.asset.metadata.tags.abstract, {

    type: "datetime",

    initialize: function (data, fieldConfig) {

        this.data = data;
        this.fieldConfig = fieldConfig;
    },

    getGridCellEditor: function (gridtype, record) {
        return new pimcore.element.helpers.gridCellEditor({
            fieldInfo: {
                layout: {
                    fieldtype: "datetime",
                    title: record.data.name
                }
            },
            elementType: "assetmetadata"
        });
    },

    getLayoutEdit: function () {
        var date = {
            width: 130,
            format: "Y-m-d"
        };

        var time = {
            format: "H:i",
            emptyText: "",
            width: 90
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
            fieldLabel: this.fieldConfig.title,
            combineErrors: false,
            items: [this.datefield, this.timefield],
            componentCls: "object_field",
            isDirty: function () {
                return this.datefield.isDirty() || this.timefield.isDirty()
            }.bind(this)
        });

        return this.component;
    },


    getCellEditValue: function () {
        return this.getValue();
    },

    getGridColumnConfig: function (field) {
        return {
            text: field.label,
            width: this.getColumnWidth(field, 300),
            sortable: false,
            dataIndex: field.key,
            getEditor: this.getListfolderEditor.bind(this, field),
            renderer: function (key, value, metaData, record) {
                if (value) {
                    var timestamp = intval(value);
                    var date = new Date(timestamp);
                    return Ext.Date.format(date, "Y-m-d H:i");
                }
                return "";
            }.bind(this, field.key)
        }
    },

    getGridCellRenderer: function(value, metaData, record, rowIndex, colIndex, store) {
        if (value) {
            var timestamp = intval(value);
            var date = new Date(timestamp);
            return Ext.Date.format(date, "Y-m-d H:i");
        }

        return value;
    },

    getListfolderEditor: function (field) {
        return new pimcore.element.helpers.gridCellEditor({
            fieldInfo: {
                layout: {
                    fieldtype: "datetime",
                    title: field.key
                }
            },
            elementType: "assetmetadata"
        });
    },

    getValue:function () {
        if (this.datefield.getValue()) {
            var value = this.datefield.getValue();
            var dateString = Ext.Date.format(value, "Y-m-d");

            if (this.timefield.getValue()) {
                var timeValue = this.timefield.getValue();
                timeValue = Ext.Date.format(timeValue, "H:i");
                dateString += " " +  timeValue;
            }
            else {
                dateString += " 00:00";
            }

            var date = Ext.Date.parseDate(dateString, "Y-m-d H:i").getTime();
            return date;
        }
        return false;
    }

});
