/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.tags.abstractManyToOneRelation");
pimcore.plugin.asset_metadata_class_definitions.bundle.tags.abstractManyToOneRelation = Class.create(pimcore.object.tags.abstract, {

    dataChanged: false,
    dataObjectFolderAllowed: false,

    initialize: function (data, fieldConfig) {

        if (data) {
            this.data = data;
        }
        this.fieldConfig = fieldConfig;
    },

    getLayoutEdit: function () {

        var href = {
            name: this.fieldConfig.name
        };

        var labelWidth = this.fieldConfig.labelWidth ? this.fieldConfig.labelWidth : 100;

        if (this.data) {
            href.value = this.data;
        }

        if (this.fieldConfig.width) {
            href.width = this.fieldConfig.width;
        } else {
            href.width = 300;
        }

        href.enableKeyEvents = true;
        href.fieldCls = "pimcore_droptarget_input";
        this.component = new Ext.form.TextField(href);

        this.component.on("render", function (el) {
            // add drop zone
            new Ext.dd.DropZone(el.getEl(), {
                reference: this,
                ddGroup: "element",
                getTargetFromEvent: function (e) {
                    return this.reference.component.getEl();
                },

                onNodeOver: function (target, dd, e, data) {
                    if (data.records.length === 1 && this.dndAllowed(data.records[0].data)) {
                        return Ext.dd.DropZone.prototype.dropAllowed;
                    }
                }.bind(this),

                onNodeDrop: this.onNodeDrop.bind(this)
            });


            el.getEl().on("contextmenu", this.onContextMenu.bind(this));

            el.getEl().on('dblclick', function () {
                s
                pimcore.helpers.openElement(this.data, this.type);
            }.bind(this));
        }.bind(this));

        var items = [this.component, {
            xtype: "button",
            iconCls: "pimcore_icon_open",
            style: "margin-left: 5px",
            handler: this.openElement.bind(this)
        }, {
            xtype: "button",
            iconCls: "pimcore_icon_delete",
            style: "margin-left: 5px",
            handler: this.empty.bind(this)
        }, {
            xtype: "button",
            iconCls: "pimcore_icon_search",
            style: "margin-left: 5px",
            handler: this.openSearchEditor.bind(this)
        }];

        this.composite = Ext.create('Ext.form.FieldContainer', {
            fieldLabel: this.fieldConfig.title,
            labelWidth: labelWidth,
            layout: 'hbox',
            items: items,
            componentCls: "object_field",
            border: false,
            style: {
                padding: 0
            }
        });

        return this.composite;
    },


    onNodeDrop: function (target, dd, e, data) {

        if (!pimcore.helpers.dragAndDropValidateSingleItem(data)) {
            return false;
        }

        data = data.records[0].data;

        if (this.dndAllowed(data)) {
            this.data = data.path;


            this.component.removeCls("strikeThrough");
            if (data.published === false) {
                this.component.addCls("strikeThrough");
            }
            this.component.setValue(data.path);

            return true;
        } else {
            return false;
        }
    },

    onContextMenu: function (e) {

        var menu = new Ext.menu.Menu();
        menu.add(new Ext.menu.Item({
            text: t('empty'),
            iconCls: "pimcore_icon_delete",
            handler: function (item) {
                item.parentMenu.destroy();

                this.empty();
            }.bind(this)
        }));

        menu.add(new Ext.menu.Item({
            text: t('open'),
            iconCls: "pimcore_icon_open",
            handler: function (item) {
                item.parentMenu.destroy();
                this.openElement();
            }.bind(this)
        }));

        menu.add(new Ext.menu.Item({
            text: t('search'),
            iconCls: "pimcore_icon_search",
            handler: function (item) {
                item.parentMenu.destroy();
                this.openSearchEditor();
            }.bind(this)
        }));

        menu.showAt(e.getXY());

        e.stopEvent();
    },

    openSearchEditor: function () {
        pimcore.helpers.itemselector(false, this.addDataFromSelector.bind(this), {
            type: [this.type]
        });
    },

    addDataFromSelector: function (data) {
        this.dataChanged = true;
        this.component.setValue(data.fullpath);
    },

    openElement: function () {
        if (this.data) {
            pimcore.helpers.openElement(this.data, this.type);
        }
    },

    empty: function () {
        this.data = null;
        this.dataChanged = true;
        this.component.setValue("");
    },

    getValue: function () {
        return this.data;
    },

    dndAllowed: function (data) {
        var elementType = data.elementType;

        if (elementType == this.type) {
            return true;
        }
        return false;
    },

    marshal: function (value) {
        return value;
    },

    unmarshal: function (value) {
        return value;
    },

    setValue: function(newValue, originator) {

        this.component.setValue(newValue);

    },

    getValue: function() {
        return this.component.getValue();
    },

    getAdditionalGridConfig: function() {
        return null;
    }

});