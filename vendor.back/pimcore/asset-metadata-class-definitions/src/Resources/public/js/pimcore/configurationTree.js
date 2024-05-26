/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.configurationTree");
pimcore.plugin.asset_metadata_class_definitions.bundle.configurationTree = Class.create({

    initialize: function () {

        this.getTabPanel();
    },

    getTabPanel: function () {

        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: "asset_metadata_class_definitions",
                title: t("asset_metadata_class_definitions_bundle-configurationpanel_title"),
                iconCls: "pimcore_icon_metadata",
                border: false,
                layout: "border",
                closable:true,
                items: [this.getTree(), this.getEditPanel()]
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem("asset_metadata_class_definitions");


            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("asset_metadata_class_definitions_panel");
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    getTree: function () {
        if (!this.tree) {
            this.store = Ext.create('Ext.data.TreeStore', {
                autoLoad: false,
                autoSync: true,
                proxy: {
                    type: 'ajax',
                    url: '/admin/asset-metadata-classdefinitions-bundle/backend/list-configurations',
                    reader: {
                        type: 'json'
                    },
                    extraParams: {
                        grouped: 1
                    }
                },
                sorters: ['text']
            });

            this.tree = Ext.create('Ext.tree.Panel', {
                store: this.store,
                region: "west",
                autoScroll:true,
                animate:false,
                containerScroll: true,
                width: 200,
                split: true,
                root: {
                    id: '0'
                },
                listeners: this.getTreeNodeListeners(),
                rootVisible: false,
                tbar: {
                    cls: 'pimcore_toolbar_border_bottom',
                    items: [
                        {
                            text: t("add"),
                            iconCls: "pimcore_icon_settings pimcore_icon_overlay_add",
                            handler: this.addField.bind(this)
                        }
                    ]
                }
            });

            this.tree.on("render", function () {
                this.getRootNode().expand();
            });
        }

        return this.tree;
    },

    getEditPanel: function () {
        if (!this.editPanel) {
            this.editPanel = Ext.create('Ext.tab.Panel', {
                region: "center",
                plugins:
                    [
                        Ext.create('Ext.ux.TabCloseMenu', {
                            showCloseAll: true,
                            showCloseOthers: true
                        }),
                        Ext.create('Ext.ux.TabReorderer', {})
                    ]
            });
        }

        return this.editPanel;
    },

    getTreeNodeListeners: function () {
        var treeNodeListeners = {
            'itemclick' : this.onTreeNodeClick.bind(this),
            "itemcontextmenu": this.onTreeNodeContextmenu.bind(this)
        };

        return treeNodeListeners;
    },

    onTreeNodeClick: function (tree, record, item, index, e, eOpts ) {
        if (!record.isLeaf()) {
            return;
        }

        this.openConfiguration(record.data.name);
    },

    openConfiguration: function (name) {

        if(Ext.getCmp("asset_metadata_class_definition_panel_" + name)) {

            this.getEditPanel().setActiveTab(Ext.getCmp("asset_metadata_class_definition_panel_" + name));
            return;
        }

        Ext.Ajax.request({
            url: "/admin/asset-metadata-classdefinitions-bundle/backend/configuration-get",
            params: {
                name: name
            },
            success: this.addFieldPanel.bind(this)
        });
    },

    addFieldPanel: function (response) {
        var data = Ext.decode(response.responseText);

        var fieldPanel = new pimcore.plugin.asset_metadata_class_definitions.bundle.configurationItem(data, this, this.openConfiguration.bind(this, data.name), "asset_metadata_class_definition_panel_");
        pimcore.layout.refresh();
    },

    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts ) {
        if (!record.isLeaf()) {
            return;
        }

        e.stopEvent();
        tree.select();

        var menu = new Ext.menu.Menu();
        menu.add(new Ext.menu.Item({
            text: t('delete'),
            iconCls: "pimcore_icon_delete",
            handler: this.deleteField.bind(this, tree, record)
        }));

        menu.showAt(e.pageX, e.pageY);
    },

    addField: function () {
        Ext.MessageBox.prompt(' ', t('enter_the_name_of_the_new_item'),
                                                        this.addFieldComplete.bind(this), null, null, "");
    },

    addFieldComplete: function (button, value, object) {

        var regresult = value.match(/[a-zA-Z]+/);

        if (button == "ok" && value.length > 2 && regresult == value) {
            Ext.Ajax.request({
                url: "/admin/asset-metadata-classdefinitions-bundle/backend/configuration-update",
                method: 'POST',
                params: {
                    name: value,
                    task: 'add'
                },
                success: function (response) {
                    this.tree.getStore().load();

                    let data = Ext.decode(response.responseText);
                    if(data && data.success) {
                        this.openConfiguration(data.name);
                    }
                }.bind(this)
            });
        }
        else if (button == "cancel") {
            return;
        }
        else {
            Ext.Msg.alert(' ', t('failed_to_create_new_item'));
        }
    },

    activate: function () {
        Ext.getCmp("pimcore_panel_tabs").setActiveItem("asset_metadata_class_definitions");
    },

    deleteField: function (tree, record) {

        Ext.Msg.confirm(t('delete'), t('delete_message'), function(btn){
            if (btn == 'yes'){
                Ext.Ajax.request({
                    url: "/admin/asset-metadata-classdefinitions-bundle/backend/configuration-delete",
                    method: 'DELETE',
                    params: {
                        name: record.data.name
                    }
                });

                this.getEditPanel().removeAll();
                record.remove();
            }
        }.bind(this));
    }

});