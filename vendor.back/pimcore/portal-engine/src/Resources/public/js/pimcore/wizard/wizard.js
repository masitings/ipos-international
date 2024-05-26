/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.PimcorePortalEngineBundle.Wizard");

pimcore.plugin.PimcorePortalEngineBundle.Wizard = Class.create({

    panelId: 'portal-engine_wizard',

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
                iconCls: "portal_engine_wizard",
                title: t("portal-engine.wizard.title"),
                border: false,
                layout: "fit",
                closable: true,
                items: [this.getPanel()]
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem(this.panelId);

            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove(this.panelId);
                if(this.polling) {
                    clearInterval(this.polling);
                }
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },
    getPanel: function() {

        let domainField = {
            fieldLabel: t('portal-engine.wizard.domain') + ' <span style="color:red;">*</span>',
            name: 'domain',
            labelWidth: 200,
            allowBlank: false
        };

        if(pimcore.settings.portalEngine && pimcore.settings.portalEngine.possiblePortalDomains) {
            domainField = {
                xtype: 'combobox',
                store: pimcore.settings.portalEngine.possiblePortalDomains,
                fieldLabel: t('portal-engine.wizard.domain') + ' <span style="color:red;">*</span>',
                name: 'domain',
                labelWidth: 200,
                allowBlank: false
            };
        }


        var formPanel = Ext.create('Ext.form.Panel', {
            title: t('portal-engine.wizard.portal-form-title'),
            iconCls: "portal_engine_wizard_tab",
            defaultType: 'textfield',
            bodyPadding: 10,
            items: [
                {
                    xtype: "hidden",
                    name: "type",
                    value: "portal"
                },
                {
                    fieldLabel: t('portal-engine.wizard.portal-name') + ' <span style="color:red;">*</span>',
                    name: 'portalName',
                    labelWidth: 200,
                    allowBlank: false
                },
                domainField,
                {
                    xtype: 'tagfield',
                    fieldLabel: t('portal-engine.wizard.available-languages') + ' <span style="color:red;">*</span>',
                    name: 'availableLanguages',
                    store: pimcore.plugin.PimcorePortalEngineBundle.wizard.helpers.getLanguagesStore(),
                    queryMode: 'local',
                    displayField: "value",
                    valueField: "key",
                    labelWidth: 200,
                    allowBlank: false
                },
                {
                    fieldLabel: t('portal-engine.wizard.logo'),
                    name: "logo",
                    fieldCls: "input_drop_target",
                    width: 600,
                    xtype: "textfield",
                    labelWidth: 200,
                    listeners: {
                        "render": this.assetDropRender
                    }
                },
                {
                    fieldLabel: t('portal-engine.wizard.login-background-image'),
                    name: "loginBackgroundImage",
                    fieldCls: "input_drop_target",
                    width: 600,
                    xtype: "textfield",
                    labelWidth: 200,
                    listeners: {
                        "render": this.assetDropRender
                    }
                }
            ]
        });

        this.tabPanel = new Ext.TabPanel( {
            cls: "portal_engine_wizard_tab_panel",
            items: [formPanel]
        });

        var menu = [];

        for([dataPoolType, dataPoolDefinition] of Object.entries(pimcore.plugin.PimcorePortalEngineBundle.wizard.dataPool)) {
            var dataPool = new pimcore.plugin.PimcorePortalEngineBundle.wizard.dataPool[dataPoolType]();
            menu.push(
                {
                    text: dataPool.getDataPoolTypeLabel(),
                    iconCls: dataPool.getIconClass(),
                    handler: function(dataPoolType) {
                        // separate instance per tab is important!
                        var dataPool = new pimcore.plugin.PimcorePortalEngineBundle.wizard.dataPool[dataPoolType]();
                        var newTab = dataPool.getPanel();
                        this.tabPanel.add(newTab);
                        this.tabPanel.setActiveTab(newTab);

                    }.bind(this, dataPoolType)
                });
        }



        var toolbar = new Ext.Toolbar({
            cls: "pimcore_main_toolbar",
            items: [
                {
                    cls: "pimcore_block_button_plus",
                    iconCls: "pimcore_icon_plus",
                    menu: menu,
                    text: t("portal-engine.wizard.add-data-pool")
                }
                ,"->",
                {
                    cls: "portal_engine_apply_wizard_button x-btn-default-toolbar-medium",
                    iconCls: "pimcore_icon_save_white",
                    text: t("portal-engine.wizard.create-portal"),
                    handler: function() {
                        var items = this.tabPanel.items.items;
                        var itemIndices = Object.keys(items);
                        var valid = true;
                        var tabsData = [];
                        for (var i = 0; i < itemIndices.length; i++) {
                            var tab = items[i];
                            var tabValid = tab.getForm().isValid();
                            if(!tabValid && valid) {
                                valid = false;
                                this.tabPanel.setActiveTab(tab);
                            }

                            var fieldValues = tab.getForm().getFieldValues();
                            delete fieldValues.iconPreview;
                            tabsData.push(fieldValues);
                        }


                        if(valid) {
                            this.mainPanel.mask(t('portal-engine.wizard.create-portal.loading'));

                            Ext.Ajax.request({
                                url: Routing.generate('pimcore_portalengine_admin_wizard_create_portal'),
                                method: 'POST',
                                params: {
                                    'data': Ext.JSON.encode(tabsData)
                                },
                                success: function(response) {
                                    var responseData = Ext.JSON.decode(response.responseText);
                                    if(!responseData.success) {
                                        pimcore.helpers.showNotification(t("error"), t("portal-engine.wizard.create-portal.failed-message"), "error");
                                    } else {
                                        this.polling = setInterval(function() {
                                            Ext.Ajax.request({
                                                url: Routing.generate('pimcore_portalengine_admin_wizard_create_portal_status'),
                                                method: 'GET',
                                                params: {
                                                    'tmpStoreKey': responseData.tmpStoreKey
                                                },
                                                success: function(response) {
                                                    var statusResponseData = Ext.JSON.decode(response.responseText);
                                                    this.mainPanel.mask('<div style="text-align: center">' + t('portal-engine.wizard.create-portal.loading') + '<br/><br/><i>' + statusResponseData.statusMessage + '</i></div>');
                                                    if(statusResponseData.isWizardFinished) {
                                                        clearInterval(this.polling);
                                                        this.mainPanel.unmask();

                                                        if(!statusResponseData.isWizardSuccess) {
                                                            pimcore.helpers.showNotification(t("error"), t("portal-engine.wizard.create-portal.failed-message"), "error");
                                                        } else {
                                                            pimcore.treenodelocator.showInTree(statusResponseData.portalDocumentId, "document");
                                                            pimcore.helpers.openDocument(statusResponseData.portalDocumentId, "page");
                                                        }
                                                    }
                                                }.bind(this),
                                                failure: function() {
                                                    clearInterval(this.polling);
                                                    this.mainPanel.unmask();
                                                }.bind(this)
                                            });
                                        }.bind(this), 1000)
                                    }
                                }.bind(this),
                                failure: function() {
                                    this.mainPanel.unmask();
                                }.bind(this)
                            });
                        }
                    }.bind(this)
                }
            ]
        });

        var panelConf = {
            autoHeight: true,
            items: [toolbar, this.tabPanel]
        };

        this.mainPanel = new Ext.Panel(panelConf);
        return this.mainPanel;
    },
    assetDropRender: function (el) {
        new Ext.dd.DropZone(el.getEl(), {
            reference: this,
            ddGroup: "element",
            getTargetFromEvent: function (e) {
                return this.getEl();
            }.bind(el),

            onNodeOver: function (target, dd, e, data) {
                if (data.records.length == 1 && data.records[0].data.elementType == "asset" && data.records[0].data.type == "image") {
                    return Ext.dd.DropZone.prototype.dropAllowed;
                }
            },

            onNodeDrop: function (target, dd, e, data) {
                if (pimcore.helpers.dragAndDropValidateSingleItem(data)) {
                    var record = data.records[0];
                    var data = record.data;

                    if (data.elementType == "asset" && data.type == "image") {
                        this.setValue(data.path);
                        return true;
                    }
                }
                return false;
            }.bind(el)
        });
    }
});
