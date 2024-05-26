/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.select");
pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.select = Class.create(pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.data, {

    type: "select",

    getGroup: function () {
        return "select";
    },

    getSpecificPanelItems: function (datax) {
        if (typeof datax.options != "object") {
            datax.options = [];
        }

        var valueStore = new Ext.data.Store({
            fields: ["key", {name: "value", allowBlank: false}],
            proxy: {
                type: 'memory'
            },
            data: datax.options
        });

        var valueGrid;

        valueGrid = Ext.create('Ext.grid.Panel', {
            itemId: "valueeditor",
            viewConfig: {
                plugins: [
                    {
                        ptype: 'gridviewdragdrop',
                        dragroup: 'objectclassselect'
                    },
                ]
            },
            tbar: [{
                xtype: "tbtext",
                text: t("selection_options")
            }, "-", {
                xtype: "button",
                iconCls: "pimcore_icon_add",
                handler: function () {
                    var u = {
                        key: "",
                        value: ""
                    };

                    let selection = this.selectionModel.getSelection();
                    var idx;
                    if (selection.length > 0) {
                        let selectedRow = selection[0];
                        idx = valueStore.indexOf(selectedRow) + 1;
                    } else {
                        idx = valueStore.getCount();
                    }
                    valueStore.insert(idx, u);
                    this.selectionModel.select(idx);
                }.bind(this)
            },
                {
                    xtype: "button",
                    iconCls: "pimcore_icon_edit",
                    handler: this.showoptioneditor.bind(this, valueStore)

                }],
            style: "margin-top: 10px",
            store: valueStore,
            selModel: Ext.create('Ext.selection.RowModel', {}),
            clicksToEdit: 1,
            columnLines: true,
            columns: [
                {
                    text: t("display_name"),
                    sortable: true,
                    dataIndex: 'key',
                    editor: new Ext.form.TextField({}),
                    renderer: function (value) {
                        return replace_html_event_attributes(strip_tags(value, 'div,span,b,strong,em,i,small,sup,sub'));
                    },
                    width: 200
                },
                {
                    text: t("value"), sortable: true, dataIndex: 'value', editor: { xtype : 'textfield', allowBlank : false },
                    width: 200
                },
                {
                    xtype: 'actioncolumn',
                    menuText: t('up'),
                    width: 40,
                    items: [
                        {
                            tooltip: t('up'),
                            icon: "/bundles/pimcoreadmin/img/flat-color-icons/up.svg",
                            handler: function (grid, rowIndex) {
                                if (rowIndex > 0) {
                                    var rec = grid.getStore().getAt(rowIndex);
                                    grid.getStore().removeAt(rowIndex);
                                    grid.getStore().insert(--rowIndex, [rec]);
                                    this.selectionModel.select(rowIndex);
                                }
                            }.bind(this)
                        }
                    ]
                },
                {
                    xtype: 'actioncolumn',
                    menuText: t('down'),
                    width: 40,
                    items: [
                        {
                            tooltip: t('down'),
                            icon: "/bundles/pimcoreadmin/img/flat-color-icons/down.svg",
                            handler: function (grid, rowIndex) {
                                if (rowIndex < (grid.getStore().getCount() - 1)) {
                                    var rec = grid.getStore().getAt(rowIndex);
                                    grid.getStore().removeAt(rowIndex);
                                    grid.getStore().insert(++rowIndex, [rec]);
                                    this.selectionModel.select(rowIndex);
                                }
                            }.bind(this)
                        }
                    ]
                },
                {
                    xtype: 'actioncolumn',
                    menuText: t('remove'),
                    width: 40,
                    items: [
                        {
                            tooltip: t('remove'),
                            icon: "/bundles/pimcoreadmin/img/flat-color-icons/delete.svg",
                            handler: function (grid, rowIndex) {
                                grid.getStore().removeAt(rowIndex);
                            }.bind(this)
                        }
                    ]
                }
            ],
            autoHeight: true,
            plugins: [
                Ext.create('Ext.grid.plugin.CellEditing', {
                    clicksToEdit: 1,
                    listeners: {
                        edit: function(editor, e) {
                            if(!e.record.get('value')) {
                                e.record.set('value', e.record.get('key'));
                            }
                        },
                        beforeedit: function(editor, e) {
                            if(e.field === 'value') {
                                return !!e.value;
                            }
                            return true;
                        },
                        validateedit: function(editor, e) {
                            if(e.field !== 'value') {
                                return true;
                            }

                            // Iterate to all store data
                            for(var i=0; i < valueStore.data.length; i++) {
                                var existingRecord = valueStore.getAt(i);
                                if(i != e.rowIdx && existingRecord.get('value') === e.value) {
                                    return false;
                                }
                            }
                            return true;
                        }
                    }
                })]
        });


        this.selectionModel = valueGrid.getSelectionModel();

        var items = [];

        items.push({
            xtype: "numberfield",
            fieldLabel: t("width"),
            name: "width",
            value: datax.width
        });

        items.push(valueGrid);
        return items;
    },

    applyData: function ($super) {

        $super();

        var options = [];

        var valueStore = this.specificPanel.getComponent("valueeditor").getStore();
        valueStore.commitChanges();
        valueStore.each(function (rec) {
            options.push({
                key: rec.get("key"),
                value: rec.get("value")
            });
        });

        this.datax.options = options;
    },

    showoptioneditor: function (valueStore) {
        var editor = new pimcore.object.helpers.optionEditor(valueStore);
        editor.edit();
    },

    applySpecialData: function (source) {
        if (source.datax) {
            if (!this.datax) {
                this.datax = {};
            }
            Ext.apply(this.datax,
                {
                    options: source.datax.options,
                    width: source.datax.width
                });
        }
    }
});