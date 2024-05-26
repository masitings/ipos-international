/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.asset.metadata.tags.manyToManyRelation");
pimcore.asset.metadata.tags.manyToManyRelation = Class.create(pimcore.asset.metadata.tags.abstract, {

    type: "manyToManyRelation",

    initialize: function (data, fieldConfig) {
        this.data = [];

        this.fieldConfig = fieldConfig;

        this.fieldConfig.classes =  this.fieldConfig.classes.filter(function (x) {
            if(x.classes == 'folder') {
                this.dataObjectFolderAllowed = true;
                return false;
            }
            return true;
        }.bind(this));

        if (data) {
            this.data = data;
        }

        var modelName = 'ObjectsMultihrefEntry';
        if (!Ext.ClassManager.isCreated(modelName)) {
            Ext.define(modelName, {
                extend: 'Ext.data.Model',
                idProperty: this.idProperty,
                fields: [
                    'id',
                    'fullpath',
                    'type',
                    'subtype',
                    'published',
                    'rowId'
                ]
            });
        }

        this.store = new Ext.data.JsonStore({
            data: this.data,
            listeners: {
                add: function () {
                    this.dataChanged = true;
                }.bind(this),
                remove: function () {
                    this.dataChanged = true;
                }.bind(this),
                clear: function () {
                    this.dataChanged = true;
                }.bind(this)
            },
            model: modelName
        });


    },

    getGridCellEditor: function (gridtype, record) {
        let config = Ext.decode(record.data.config);
        return new pimcore.element.helpers.gridCellEditor({
            fieldInfo: {
                layout: config
            },
            elementType: "assetmetadata"
        });
    },

    getLayoutEdit: function () {
        var autoHeight = false;
        if (intval(this.fieldConfig.height) < 15) {
            autoHeight = true;
        }
        var cls = 'object_field';

        var toolbarItems = this.getEditToolbarItems();

        var columns = this.getVisibleColumns();
        columns.push({
                xtype: 'actioncolumn',
                menuText: t('up'),
                hideable: false,
                width: 40,
                items: [
                    {
                        tooltip: t('up'),
                        icon: "/bundles/pimcoreadmin/img/flat-color-icons/up.svg",
                        handler: function (grid, rowIndex) {
                            if (rowIndex > 0) {
                                var rec = grid.getStore().getAt(rowIndex);
                                grid.getStore().removeAt(rowIndex);
                                grid.getStore().insert(rowIndex - 1, [rec]);
                            }
                        }.bind(this)
                    }
                ]
            },
            {
                xtype: 'actioncolumn',
                menuText: t('down'),
                width: 40,
                hideable: false,
                items: [
                    {
                        tooltip: t('down'),
                        icon: "/bundles/pimcoreadmin/img/flat-color-icons/down.svg",
                        handler: function (grid, rowIndex) {
                            if (rowIndex < (grid.getStore().getCount() - 1)) {
                                var rec = grid.getStore().getAt(rowIndex);
                                grid.getStore().removeAt(rowIndex);
                                grid.getStore().insert(rowIndex + 1, [rec]);
                            }
                        }.bind(this)
                    }
                ]
            },
            {
                xtype: 'actioncolumn',
                menuText: t('open'),
                width: 40,
                hideable: false,
                items: [{
                    tooltip: t('open'),
                    icon: "/bundles/pimcoreadmin/img/flat-color-icons/open_file.svg",
                    handler: function (grid, rowIndex) {
                        var data = grid.getStore().getAt(rowIndex);
                        var subtype = data.data.subtype;
                        if (data.data.type == "object" && data.data.subtype != "folder") {
                            subtype = "object";
                        }
                        pimcore.helpers.openElement(data.data.id, data.data.type, subtype);
                    }.bind(this)
                }]
            },
            {
                xtype: 'actioncolumn',
                menuText: t('remove'),
                width: 40,
                hideable: false,
                items: [{
                    tooltip: t('remove'),
                    icon: "/bundles/pimcoreadmin/img/flat-color-icons/delete.svg",
                    handler: function (grid, rowIndex) {
                        grid.getStore().removeAt(rowIndex);
                    }.bind(this)
                }]
            });

        this.component = new Ext.grid.GridPanel({
            store: this.store,
            border: true,
            style: "margin-bottom: 10px",

            selModel: Ext.create('Ext.selection.RowModel', {}),
            viewConfig: {
                markDirty: false,
                plugins: {
                    ptype: 'gridviewdragdrop',
                    draggroup: 'element'
                },
                listeners: {
                    drop: function (node, data, dropRec, dropPosition) {
                        // this is necessary to avoid endless recursion when long lists are sorted via d&d
                        // TODO: investigate if there this is already fixed 6.2
                        if (this.asset.toolbar && this.asset.toolbar.items && this.asset.toolbar.items.items) {
                            this.asset.toolbar.items.items[0].focus();
                        }
                    }.bind(this),
                    refresh: function (gridview) {
                        this.requestNicePathData(this.store.data);
                    }.bind(this)
                }
            },
            columns: {
                defaults: {
                    sortable: false
                },
                items: columns
            },
            componentCls: cls,
            tbar: {
                items: toolbarItems,
                ctCls: "pimcore_force_auto_width",
                cls: "pimcore_force_auto_width"
            },
            width: this.fieldConfig.width,
            height: this.fieldConfig.height,
            autoHeight: autoHeight,
            bodyCssClass: "pimcore_object_tag_multihref"
        });

        this.component.on("rowcontextmenu", this.onRowContextmenu);
        this.component.reference = this;

        this.component.on("afterrender", function () {

            var dropTargetEl = this.component.getEl();
            var gridDropTarget = new Ext.dd.DropZone(dropTargetEl, {
                ddGroup: 'element',
                getTargetFromEvent: function (e) {
                    return this.component.getEl().dom;
                    //return e.getTarget(this.grid.getView().rowSelector);
                }.bind(this),

                onNodeOver: function (overHtmlNode, ddSource, e, data) {
                    try {
                        var returnValue = Ext.dd.DropZone.prototype.dropAllowed;
                        data.records.forEach(function (record) {
                            var fromTree = this.isFromTree(ddSource);
                            if (!this.dndAllowed(record.data, fromTree)) {
                                returnValue = Ext.dd.DropZone.prototype.dropNotAllowed;
                            }
                        }.bind(this));

                        return returnValue;
                    } catch (e) {
                        console.log(e);
                    }
                }.bind(this),

                onNodeDrop: function (target, dd, e, data) {

                    try {
                        this.nodeElement = data;
                        var fromTree = this.isFromTree(dd);
                        var toBeRequested = new Ext.util.Collection();

                        data.records.forEach(function (record) {
                            var data = record.data;
                            if (this.dndAllowed(data, fromTree)) {
                                if (data["grid"] && data["grid"] == this.component) {
                                    var rowIndex = this.component.getView().findRowIndex(e.target);
                                    if (rowIndex !== false) {
                                        var rec = this.store.getAt(data.rowIndex);
                                        this.store.removeAt(data.rowIndex);
                                        toBeRequested.add(this.store.insert(rowIndex, [rec]));
                                        this.requestNicePathData(toBeRequested);
                                    }
                                } else {
                                    var initData = {
                                        id: data.id,
                                        fullpath: data.path,
                                        type: data.elementType,
                                        published: data.published
                                    };

                                    if (initData.type === "object") {
                                        if (data.className) {
                                            initData.subtype = data.className;
                                        } else {
                                            initData.subtype = "folder";
                                        }
                                    }

                                    if (initData.type === "document" || initData.type === "asset") {
                                        initData.subtype = data.type;
                                    }

                                    // check for existing element
                                    if (!this.elementAlreadyExists(initData.id, initData.type)) {
                                        toBeRequested.add(this.store.add(initData));
                                    }
                                }
                            }
                        }.bind(this));

                        if(toBeRequested.length) {
                            this.requestNicePathData(toBeRequested);
                            return true;
                        }

                        return false;

                    } catch (e) {
                        console.log(e);
                        return false;
                    }
                }.bind(this)
            });
        }.bind(this));

        return this.component;
    },


    getEditToolbarItems: function () {

        var toolbarItems = [
            {
                xtype: "tbspacer",
                width: 24,
                height: 24,
                cls: "pimcore_icon_droptarget"
            },
            {
                xtype: "tbtext",
                text: "<b>" + this.fieldConfig.title + "</b>"
            },
            "->"
        ];
        toolbarItems = toolbarItems.concat(this.getFilterEditToolbarItems());
        toolbarItems = toolbarItems.concat([
            {
                xtype: "button",
                iconCls: "pimcore_icon_delete",
                handler: this.empty.bind(this)
            },
            {
                xtype: "button",
                iconCls: "pimcore_icon_search",
                handler: this.openSearchEditor.bind(this)
            }
        ]);

        if (this.fieldConfig.assetsAllowed) {
            toolbarItems.push({
                xtype: "button",
                cls: "pimcore_inline_upload",
                iconCls: "pimcore_icon_upload",
                handler: this.uploadDialog.bind(this)
            });
        }

        return toolbarItems;
    },

    getFilterEditToolbarItems: function () {
        return [
            {
                xtype: 'textfield',
                hidden: true,
                cls: 'relations_grid_filter_input',
                width: '250px',
                listeners:
                    {
                        keyup: {
                            fn: this.filterStore.bind(this),
                            element: "el"
                        },
                        blur: function (filterField) {
                            /* do not hide filter if filter is active */
                            if (filterField.getValue().length === 0) {
                                this.hideFilterInput(filterField);
                            }
                        }.bind(this)
                    }
            },
            {
                xtype: "button",
                iconCls: "pimcore_icon_filter",
                cls: "relations_grid_filter_btn",
                handler: this.showFilterInput.bind(this)
            }
        ];
    },

    showFilterInput: function (filterBtn) {
        var filterInput = filterBtn.previousSibling("field[cls~=relations_grid_filter_input]");
        filterInput.show();
        filterInput.focus();
        filterBtn.hide();
    },

    hideFilterInput: function (filterInput) {
        var filterBtn = filterInput.nextSibling("button[cls~=relations_grid_filter_btn]");
        filterBtn.show();
        filterInput.hide();
    },

    filterStore: function (e) {
        var visibleFieldDefinitions = this.fieldConfig.visibleFieldDefinitions || {};
        var visibleFields = Ext.Object.getKeys(visibleFieldDefinitions);
        var metaDataFields = this.fieldConfig.columnKeys || [];
        var searchColumns = Ext.Array.merge(visibleFields, metaDataFields);

        /* always search in path (relations), fullpath (object relations) and id */
        searchColumns.push("path");
        searchColumns.push("fullpath");
        searchColumns.push("id");

        searchColumns = Ext.Array.unique(searchColumns);

        var q = Ext.get(e.target).getValue().toLowerCase();
        var searchFilter = new Ext.util.Filter({
            filterFn: function (item) {
                for (var column in item.data) {
                    var value = item.data[column];
                    /* skip none-search columns and null values */
                    if (searchColumns.indexOf(column) < 0 || !value) {
                        continue;
                    }
                    /* links */
                    if (!!visibleFieldDefinitions[column] && visibleFieldDefinitions[column].fieldtype === "link") {
                        value = [value.text, value.title, value.path].join(" ");
                    }
                    /* numbers, texts */
                    value = String(value).toLowerCase();
                    if (value.indexOf(q) >= 0) {
                        return true;
                    }
                }
                return false;
            }
        });
        this.store.clearFilter();
        this.store.filter(searchFilter);
    },

    empty: function () {
        this.store.removeAll();
    },

    removeElement: function (index, item) {
        this.getStore().removeAt(index);
        item.parentMenu.destroy();
    },

    empty: function () {
        this.store.removeAll();
    },

    removeElement: function (index, item) {
        this.getStore().removeAt(index);
        item.parentMenu.destroy();
    },

    openSearchEditor: function () {

        var allowedTypes = [];
        var allowedSpecific = {};
        var allowedSubtypes = {};
        var i;

        if (this.fieldConfig.objectsAllowed) {
            allowedTypes.push("object");
            allowedSubtypes.object = [];
            if (this.fieldConfig.classes != null && this.fieldConfig.classes.length > 0) {
                allowedSpecific.classes = [];
                allowedSubtypes.object.push("object", "variant");
                for (i = 0; i < this.fieldConfig.classes.length; i++) {
                    allowedSpecific.classes.push(this.fieldConfig.classes[i].classes);

                }
            }
            if(this.dataObjectFolderAllowed) {
                allowedSubtypes.object.push("folder");
            }

            if(allowedSubtypes.length == 0) {
                allowedSubtypes.object = ["object", "folder", "variant"];
            }
        }
        if (this.fieldConfig.assetsAllowed) {
            allowedTypes.push("asset");
            if (this.fieldConfig.assetTypes != null && this.fieldConfig.assetTypes.length > 0) {
                allowedSubtypes.asset = [];
                for (i = 0; i < this.fieldConfig.assetTypes.length; i++) {
                    allowedSubtypes.asset.push(this.fieldConfig.assetTypes[i].assetTypes);
                }
            }
        }
        if (this.fieldConfig.documentsAllowed) {
            allowedTypes.push("document");
            if (this.fieldConfig.documentTypes != null && this.fieldConfig.documentTypes.length > 0) {
                allowedSubtypes.document = [];
                for (i = 0; i < this.fieldConfig.documentTypes.length; i++) {
                    allowedSubtypes.document.push(this.fieldConfig.documentTypes[i].documentTypes);
                }
            }
        }

        pimcore.helpers.itemselector(true, this.addDataFromSelector.bind(this), {
                type: allowedTypes,
                subtype: allowedSubtypes,
                specific: allowedSpecific
            },
            {
                context: Ext.apply({scope: "objectEditor"}, this.getContext())
            });

    },

    getVisibleColumns: function () {
        var columns = [
            {text: 'ID', dataIndex: 'id', width: 50},
            {text: t("reference"), dataIndex: 'fullpath', flex: 200 /*, renderer:this.fullPathRenderCheck.bind(this) */ },
            {text: t("type"), dataIndex: 'type', width: 100},
            {text: t("subtype"), dataIndex: 'subtype', width: 100},
        ];

        return columns;
    },

    getCellEditValue: function () {
        let value = [];
        this.store.each(function(rec) {
            value.push(rec.data);
        })

        return value;
    },

    getGridColumnConfig: function (field) {
        return {
            text: field.label,
            width: this.getColumnWidth(field, 300),
            sortable: false,
            dataIndex: field.key,
            getEditor: this.getListfolderEditor.bind(this, field),
            renderer: function (key, value, metaData, record) {
                return this.getGridCellDisplayValue(value);
            }.bind(this, field.key)
        }
    },

    getGridCellDisplayValue: function(value) {
        if (value) {

            var result = [];

            for (let i = 0; i < value.length && i < 10; i++) {
                var item = value[i];
                result.push(item["fullpath"]);
            }
            return result.join("<br />");
        } else {
            return "";
        }


        return value;
    },

    getGridCellRenderer: function (value, metaData, record, rowIndex, colIndex, store) {
        return this.getGridCellDisplayValue(value);
    },


    getListfolderEditor: function (field) {
        let config = Ext.decode(field.layout.config);
        return new pimcore.element.helpers.gridCellEditor({
            fieldInfo: {
                layout: config
            },
            elementType: "assetmetadata"
        });
    },

    getValue: function () {
        return this.getCellEditValue();
    },

    onRowContextmenu: function (grid, record, tr, rowIndex, e, eOpts) {

        var menu = new Ext.menu.Menu();
        var data = record;

        menu.add(new Ext.menu.Item({
            text: t('remove'),
            iconCls: "pimcore_icon_delete",
            handler: this.reference.removeElement.bind(this, rowIndex)
        }));

        menu.add(new Ext.menu.Item({
            text: t('open'),
            iconCls: "pimcore_icon_open",
            handler: function (data, item) {

                item.parentMenu.destroy();

                var subtype = data.data.subtype;
                if (data.data.type == "object" && data.data.subtype != "folder") {
                    subtype = "object";
                }
                pimcore.helpers.openElement(data.data.id, data.data.type, subtype);
            }.bind(this, data)
        }));

        menu.add(new Ext.menu.Item({
            text: t('search'),
            iconCls: "pimcore_icon_search",
            handler: function (item) {
                item.parentMenu.destroy();
                this.openSearchEditor();
            }.bind(this.reference)
        }));

        e.stopEvent();
        menu.showAt(e.getXY());
    },

    uploadDialog: function () {
        pimcore.helpers.assetSingleUploadDialog(this.fieldConfig.assetUploadPath, "path", function (res) {
            try {
                var data = Ext.decode(res.response.responseText);
                if (data["id"]) {
                    var toBeRequested = new Ext.util.Collection();
                    toBeRequested.add(this.store.add({
                        id: data["id"],
                        fullpath: data["fullpath"],
                        type: "asset",
                        subtype: data["type"]
                    }));
                    this.requestNicePathData(toBeRequested);
                }
            } catch (e) {
                console.log(e);
            }
        }.bind(this), null, this.context);
    },

    requestNicePathData: function(toBeRequested) {

    },


    isFromTree: function (ddSource) {
        var klass = Ext.getClass(ddSource);
        var className = klass.getName();
        var fromTree = className == "Ext.tree.ViewDragZone";
        return fromTree;
    },

    addDataFromSelector: function (items) {
        if (items.length > 0) {
            toBeRequested = new Ext.util.Collection();

            for (var i = 0; i < items.length; i++) {
                if (!this.elementAlreadyExists(items[i].id, items[i].type)) {

                    var subtype = items[i].subtype;
                    if (items[i].type == "object") {
                        if (items[i].subtype == "object") {
                            if (items[i].classname) {
                                subtype = items[i].classname;
                            }
                        }
                    }

                    toBeRequested.add(this.store.add({
                        id: items[i].id,
                        fullpath: items[i].fullpath,
                        type: items[i].type,
                        subtype: subtype,
                        published: items[i].published
                    }));
                }
            }

            this.requestNicePathData(toBeRequested);
        }
    },

    dndAllowed: function (data, fromTree) {

        var i;

        // check if data is a treenode, if not check if the source is the same grid because of the reordering
        if (!fromTree) {
            if (data["grid"] && data["grid"] == this.component) {
                return true;
            }
            return false;
        }

        var elementType = data.elementType;
        var isAllowed = false;
        var subType;

        if (elementType == "object" && this.fieldConfig.objectsAllowed) {

            if(data.type == 'folder') {
                if(this.dataObjectFolderAllowed || this.fieldConfig.classes.length <= 0) {
                    isAllowed = true;
                }
            } else {
                var classname = data.className;

                isAllowed = false;
                if (this.fieldConfig.classes != null && this.fieldConfig.classes.length > 0) {
                    for (i = 0; i < this.fieldConfig.classes.length; i++) {
                        if (this.fieldConfig.classes[i].classes == classname) {
                            isAllowed = true;
                            break;
                        }
                    }
                } else {
                    if(!this.dataObjectFolderAllowed) {
                        isAllowed = true;
                    }
                }
            }
        } else if (elementType == "asset" && this.fieldConfig.assetsAllowed) {
            subType = data.type;
            isAllowed = false;
            if (this.fieldConfig.assetTypes != null && this.fieldConfig.assetTypes.length > 0) {
                for (i = 0; i < this.fieldConfig.assetTypes.length; i++) {
                    if (this.fieldConfig.assetTypes[i].assetTypes == subType) {
                        isAllowed = true;
                        break;
                    }
                }
            } else {
                //no asset types configured - allow all
                isAllowed = true;
            }

        } else if (elementType == "document" && this.fieldConfig.documentsAllowed) {
            subType = data.type;
            isAllowed = false;
            if (this.fieldConfig.documentTypes != null && this.fieldConfig.documentTypes.length > 0) {
                for (i = 0; i < this.fieldConfig.documentTypes.length; i++) {
                    if (this.fieldConfig.documentTypes[i].documentTypes == subType) {
                        isAllowed = true;
                        break;
                    }
                }
            } else {
                //no document types configured - allow all
                isAllowed = true;
            }
        }
        return isAllowed;

    },

    elementAlreadyExists: function (id, type) {

        // check max amount in field
        if (this.fieldConfig["maxItems"] && this.fieldConfig["maxItems"] >= 1) {
            if (this.store.getCount() >= this.fieldConfig.maxItems) {
                Ext.Msg.alert(t("error"), t("limit_reached"));
                return true;
            }
        }

        // check for existing element
        var result = this.store.queryBy(function (id, type, record, rid) {
            if (record.data.id == id && record.data.type == type) {
                return true;
            }
            return false;
        }.bind(this, id, type));

        if (result.length < 1) {
            return false;
        }
        return true;
    },


    prepareBatchEditLayout: function(layout) {
        let config = layout["config"];
        config = Ext.decode(config);
        Ext.apply(layout, config);
        return layout;
    }


});
