/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.PimcorePortalEngineBundle.collections.list");

pimcore.plugin.PimcorePortalEngineBundle.collections.list = Class.create({

    panelId: 'portal-engine_collection_list',

    initialize: function () {
        this.getTabPanel();
    },

    activate: function () {
        const tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem(this.panelId);
    },

    getTabPanel: function () {

        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: this.panelId,
                iconCls: "portal-engine_admin_collections",
                title: t("portal-engine.collectionlist.title"),
                border: false,
                layout: "fit",
                closable: true,
                items: [this.getGrid()]
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem(this.panelId);

            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove(this.panelId);
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    getGrid: function () {
        var user = pimcore.globalmanager.get("user");

        var itemsPerPage = pimcore.helpers.grid.getDefaultPageSize();
        const store = pimcore.helpers.grid.buildDefaultStore(
            Routing.generate('pimcore_portalengine_admin_collection_grid'),
            [
                'id', {name: 'name', allowBlank: false}, 'userId', 'itemCount', 'creationDate', 'currentSiteId'
            ],
            itemsPerPage
        );

        const filterField = Ext.create("Ext.form.TextField", {
            width: 200,
            style: "margin: 0 10px 0 0;",
            enableKeyEvents: true,
            listeners: {
                "keydown" : function (store, field, key) {
                    if (key.getKey() === key.ENTER) {
                        var input = field;
                        var proxy = store.getProxy();
                        proxy.extraParams.filter = input.getValue();
                        store.load();
                    }
                }.bind(this, store)
            }
        });

        const pagingtoolbar = pimcore.helpers.grid.buildDefaultPagingToolbar(store);

        var typesColumns = [
            {text: t("id"), flex: 10, sortable: true, dataIndex: 'id'},
            {text: t("portal-engine.collectionlist.portal"), flex: 100, sortable: true, dataIndex: 'currentSiteId'},
            {text: t("name"), flex: 200, sortable: true, dataIndex: 'name', editor: new Ext.form.TextField({})},
            {text: t("portal-engine.collectionlist.owner"), flex: 200, sortable: true, dataIndex: 'userId'},
            {text: t("portal-engine.collectionlist.itemCount"), flex: 50, sortable: false, dataIndex: 'itemCount'},
            {text: t("creationDate"), flex: 100, sortable: true, dataIndex: 'creationDate', editable: false,
                renderer: function(d) {
                    if (d !== undefined) {
                        var date = new Date(d * 1000);
                        return Ext.Date.format(date, "Y-m-d H:i:s");
                    } else {
                        return "";
                    }
                }
            },
            {
                xtype: 'actioncolumn',
                menuText: t('open'),
                width: 40,
                hideable: false,
                items: [
                    {
                        tooltip: t('open'),
                        icon: "/bundles/pimcoreadmin/img/flat-color-icons/open_file.svg",
                        handler: function (grid, rowIndex) {
                            var data = grid.getStore().getAt(rowIndex);
                            this.openCollectionTree(data.id);
                        }.bind(this)
                    }
                ]
            },
            {
                xtype: 'actioncolumn',
                menuText: t('portal-engine.collectionlist.share'),
                width: 40,
                hideable: false,
                hidden: !user.isAllowed("share_configurations"),
                items: [
                    {
                        tooltip: t('portal-engine.collectionlist.sharing'),
                        icon: "/bundles/pimcoreadmin/img/flat-color-icons/targetgroup.svg",
                        handler: function (grid, rowIndex) {
                            var data = grid.getStore().getAt(rowIndex);
                            this.loadShareInformation(data.id);
                        }.bind(this)
                    }
                ]
            },
            {
                xtype: 'actioncolumn',
                menuText: t('delete'),
                width: 30,
                items: [{
                    tooltip: t('delete'),
                    icon: "/bundles/pimcoreadmin/img/flat-color-icons/delete.svg",
                    handler: function (grid, rowIndex) {
                        Ext.Msg.confirm(t('portal-engine.collectionlist.delete.title'), t('portal-engine.collectionlist.delete.text'), function(buttonId) {
                            if(buttonId === 'yes') {
                                grid.getStore().removeAt(rowIndex);
                            }
                        });
                    }.bind(this)
                }]
            }
        ];

        this.cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1
        });


        const checkboxShowAll = Ext.create('Ext.form.Checkbox', {
            name: "showAll",
            style: "margin-bottom: 5px; margin-left: 5px; color: #e3e4e5",
            hidden: !user.admin,
            checked: this.showAll,
            boxLabel: t("portal-engine.collectionlist.show-all"),
            listeners: {
                "change" : function (store, pagingtoolbar, field, checked) {
                    store.getProxy().setExtraParam("showAll", checked);
                    pagingtoolbar.moveFirst();
                }.bind(this, store, pagingtoolbar)
            }
        });

        var toolbar = Ext.create('Ext.Toolbar', {
            cls: 'pimcore_main_toolbar',
            items: [
                {
                    text: t('add'),
                    handler: this.openWizard.bind(this),
                    iconCls: "pimcore_icon_add"
                },
                "->",
                checkboxShowAll,
                "-",
                {
                    text: t("filter") + "/" + t("search"),
                    xtype: "tbtext",
                    style: "margin: 0 10px 0 0;"
                },
                filterField
            ]
        });

        this.grid = Ext.create('Ext.grid.Panel', {
            scrollable: true,
            store: store,
            columns: {
                items: typesColumns,
                defaults: {
                    renderer: Ext.util.Format.htmlEncode
                },
            },
            selModel: Ext.create('Ext.selection.RowModel', {}),
            plugins: [
                this.cellEditing
            ],

            trackMouseOver: true,
            columnLines: true,
            bbar: pagingtoolbar,
            bodyCls: "pimcore_editable_grid",
            stripeRows: true,
            tbar: toolbar,
            viewConfig: {
                forceFit: true,
            }
        });
        store.load();

        return this.grid;
    },

    openWizard: function () {
        const portalsStore = new Ext.data.JsonStore({
            proxy: {
                type: 'ajax',
                url: Routing.generate('pimcore_portalengine_admin_collection_portallist'),
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            autoLoad: true,
            fields: ['id', 'name']
        });

        const portalCombo = Ext.create('Ext.form.ComboBox', {
            store: portalsStore,
            mode: "local",
            name: 'portal',
            fieldLabel: t('portal-engine.collectionlist.portal'),
            width: 400,
            queryMode: "local",
            typeAhead: false,
            editable: false,
            displayField: 'name',
            valueField: 'id',
            allowBlank: false,
            forceSelection: true,
            triggerAction: "all"
        });

        const wizardForm = Ext.create('Ext.form.FormPanel', {
            bodyStyle: "padding:10px;",
            items: [portalCombo, {
                xtype: 'textfield',
                name: 'name',
                width: 400,
                emptyText: "",
                allowBlank: false,
                fieldLabel: t("name")
            }]
        });

        const wizardWindow = Ext.create('Ext.Window', {
            width: 450,
            modal: true,
            title: t('portal-engine.collectionlist.add-new'),
            items: [wizardForm],
            buttons: [{
                text: t("save"),
                iconCls: "pimcore_icon_accept",
                handler: this.saveWizard.bind(this, wizardForm)
            }]
        });
        wizardForm.parentWindow = wizardWindow;

        wizardWindow.show();
    },

    saveWizard: function (wizardForm) {
        const values = wizardForm.getForm().getFieldValues();

        if(wizardForm.getForm().isValid()) {
            const record = {
                name: values['name'],
                portal: values['portal']
            };
            this.grid.store.insert(0, record);
            this.grid.getView().scrollTo(0, 0);

            wizardForm.parentWindow.close();
        }
    },

    openCollectionTree: function(collectionId) {

        Ext.Ajax.request({
            url: Routing.generate('pimcore_portalengine_admin_collection_tree'),
            params: {
                collectionId: collectionId
            },
            success: function(response) {
                let rdata = Ext.decode(response.responseText);
                rdata.moveRight = true;
                const t = new pimcore.plugin.PimcorePortalEngineBundle.collections.tree(rdata);
                pimcore.plugin.PimcorePortalEngineBundle.collections.rememberOpenTree(rdata.collectionId);
            }.bind(this)
        });

    },

    loadShareInformation: function(collectionId) {
        Ext.Ajax.request({
            url: Routing.generate('pimcore_portalengine_admin_collection_sharelist'),
            params: {
                collectionId: collectionId
            },
            success: function(collectionId, response) {
                let rdata = Ext.decode(response.responseText);
                if(rdata.success) {
                    this.openShareWizard(collectionId, rdata.data);
                }
            }.bind(this, collectionId)
        });
    },

    openShareWizard: function(collectionId, collectionShareList) {
        let addedIds = [];

        const userStore = new Ext.data.JsonStore({
            proxy: {
                type: 'ajax',
                url: Routing.generate('pimcore_portalengine_admin_collection_usersearch'),
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                },
                extraParams: {
                    collectionId: collectionId
                }
            },
            autoLoad: false,
            fields: ['id', 'name', 'type']
        });

        const panelHeaderRow = Ext.create('Ext.Panel', {
            layout: {
                type: 'hbox',
                align: 'middle'
            },
            items: [
                {
                    xtype: 'label',
                    html: '',
                    width: 150,
                },
                {
                    xtype: 'label',
                    html: t('portal-engine.collectionlist.sharing.none'),
                    style: 'text-align: center',
                    width: 80,
                },
                {
                    xtype: 'label',
                    html: t('portal-engine.collectionlist.sharing.read'),
                    style: 'text-align: center',
                    width: 80,
                },
                {
                    xtype: 'label',
                    html: t('portal-engine.collectionlist.sharing.edit'),
                    style: 'text-align: center',
                    width: 80,
                }
            ]
        });

        const permissionsPanel = Ext.create('Ext.form.FormPanel', {
            maxHeight: 200,
            scrollable: true,
            items: []
        });

        const userCombo = Ext.create('Ext.form.ComboBox', {
            store: userStore,
            name: 'portal',
            fieldLabel: t('portal-engine.collectionlist.sharing.user_group'),
            width: 400,
            labelWidth: 180,
            displayField: 'name',
            valueField: 'id',
            triggerAction: 'query',
            typeAhead: true,
            minChars: 2,
            listeners: {
                select: function(permissionPanel, addedIds, combo, record) {
                    if(record) {

                        addedIds.push(record.data.id);
                        const newCollectionShare = {
                            shareRecipientId: record.data.id,
                            shareRecipient: record.data.name,
                            permission: 'none'
                        }
                        this.addPermissionRow(permissionsPanel, newCollectionShare);

                        const store = combo.getStore();
                        store.getProxy().setExtraParam("addedIds[]", addedIds);
                        store.reload();
                        combo.reset();
                    }
                }.bind(this, permissionsPanel, addedIds)
            }
        });

        const shareWindow = Ext.create('Ext.Window', {
            width: 550,
            modal: true,
            title: t('portal-engine.collectionlist.sharing'),
            items: [{
                xtype: 'panel',
                style: 'padding: 20px',
                items: [
                    panelHeaderRow,
                    {
                        xtype: 'box',
                        autoEl : { tag : 'hr' }
                    },
                    permissionsPanel,
                    {
                        xtype: 'box',
                        autoEl : { tag : 'hr' }
                    },
                    userCombo
                ]
            }],
            buttons: [{
                text: t("save"),
                iconCls: "pimcore_icon_accept",
                handler: this.applyPermissions.bind(this, collectionId, permissionsPanel)
            }]
        });
        permissionsPanel.parentWindow = shareWindow;

        if(Array.isArray(collectionShareList)) {

            collectionShareList.forEach(function(collectionShare) {
                addedIds.push(collectionShare.shareRecipientId);
                this.addPermissionRow(permissionsPanel, collectionShare);
            }.bind(this));

        }

        userStore.getProxy().setExtraParam("addedIds[]", addedIds);
        userStore.load();

        shareWindow.show();
    },

    addPermissionRow: function(permissionPanel, collectionShare) {
        const row = Ext.create('Ext.Panel', {
            layout: {
                type: 'hbox',
                align: 'middle'
            },
            items: [
                {
                    xtype: 'label',
                    html: collectionShare.shareRecipient,
                    width: 150,
                },
                {
                    xtype: 'radiofield',
                    name : 'shares[' + collectionShare.shareRecipientId + ']',
                    inputValue: 'none',
                    cls: 'portal-engine_admin_collections_permission-radio',
                    width: 80,
                    value: collectionShare.permission === 'none'
                },
                {
                    xtype: 'radiofield',
                    name : 'shares[' + collectionShare.shareRecipientId + ']',
                    inputValue: 'read',
                    cls: 'portal-engine_admin_collections_permission-radio',
                    width: 80,
                    value: collectionShare.permission === 'read'
                },
                {
                    xtype: 'radiofield',
                    name : 'shares[' + collectionShare.shareRecipientId + ']',
                    inputValue: 'edit',
                    cls: 'portal-engine_admin_collections_permission-radio',
                    width: 80,
                    value: collectionShare.permission === 'edit'
                }
            ]
        });
        permissionPanel.add(row);
    },

    applyPermissions: function(collectionId, permissionsPanel) {

        let values = permissionsPanel.getForm().getFieldValues();
        values.collectionId = collectionId;

        Ext.Ajax.request({
            url: Routing.generate('pimcore_portalengine_admin_collection_update_share'),
            params: values,
            success: function(permissionsPanel, response) {
                var rdata = Ext.decode(response.responseText);

                if (!rdata || !rdata.success) {
                    pimcore.helpers.showNotification(t("error"), t("portal-engine.collectionlist.sharing.update_error"), "error", t(rdata.message));
                } else {
                    permissionsPanel.parentWindow.close();
                }
            }.bind(this, permissionsPanel)
        });
    }

});
