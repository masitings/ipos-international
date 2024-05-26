/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.PimcorePortalEngineBundle");

pimcore.plugin.PimcorePortalEngineBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.PimcorePortalEngineBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        var user = pimcore.globalmanager.get("user");

        if(user.isAllowed('portal-engine_collection-access')) {
            Ext.Ajax.request({
                url: Routing.generate('pimcore_portalengine_admin_collection_check_user_assignment'),
                success: function(response) {
                    let rdata = Ext.decode(response.responseText);
                    if(rdata.success) {
                        var fileMenu = pimcore.globalmanager.get("layout_toolbar").fileMenu;

                        fileMenu.insert(2, {
                            text: t("portal-engine.collectionlist.menu"),
                            iconCls: "portal-engine_admin_collections",
                            handler: function () {
                                try {
                                    pimcore.globalmanager.get("portal-engine_collection_list").activate();
                                }
                                catch (e) {
                                    pimcore.globalmanager.add("portal-engine_collection_list", new pimcore.plugin.PimcorePortalEngineBundle.collections.list());
                                }
                            }
                        });
                    } else {
                        console.log('Admin user has no assigned portal user.');
                    }

                    const openTrees = pimcore.plugin.PimcorePortalEngineBundle.collections.getOpenTrees();
                    openTrees.forEach(function(collectionId) {
                        Ext.Ajax.request({
                            url: Routing.generate('pimcore_portalengine_admin_collection_tree'),
                            params: {
                                collectionId: collectionId
                            },
                            success: function(collectionId, response) {
                                let rdata = Ext.decode(response.responseText);
                                if(rdata.success) {
                                    rdata.moveRight = true;
                                    const t = new pimcore.plugin.PimcorePortalEngineBundle.collections.tree(rdata);
                                } else {
                                    pimcore.plugin.PimcorePortalEngineBundle.collections.forgetOpenTree(collectionId);
                                }

                            }.bind(this, collectionId)
                        });
                    });

                }.bind(this)
            });
        }

        if (user.isAllowed("portal-engine_wizard")) {
            let settingsMenu = pimcore.globalmanager.get("layout_toolbar").settingsMenu;
            if (settingsMenu) {
                settingsMenu.insert(settingsMenu.items.length + 1, {
                    text: t('portal-engine.wizard.menu'),
                    iconCls: "portal_engine_wizard",
                    cls: "pimcore_main_menu",
                    handler: function () {
                        try {
                            pimcore.globalmanager.get("portal-engine_wizard").activate();
                        }
                        catch (e) {
                            pimcore.globalmanager.add("portal-engine_wizard", new pimcore.plugin.PimcorePortalEngineBundle.Wizard());
                        }
                    }
                });
                settingsMenu.updateLayout();
            }
        }
    },

    postOpenDocument: function(document, type){

        var checkFrontendBuildRunning = function(callback) {
            Ext.Ajax.request({
                url: Routing.generate('pimcore_portalengine_admin_is_portal'),
                params: {portalId: document.id},
                success: function(response) {
                    var responseJson = Ext.util.JSON.decode(response.responseText);
                    if(responseJson.success && responseJson.isPortal) {

                        callback(responseJson);
                    }
                }
            });
        };

        checkFrontendBuildRunning(function(responseJson) {

            document.tab.items.items[0].add({
                text: '<img src="/bundles/pimcoreportalengine/img/loading.gif" class="portal_engine_frontend_build_loading_icon"/><span class="portal_engine_frontend_build_loading_text">' + t('portal-engine.update-frontend-build-running') + '</span>',
                iconCls: 'portal_engine_icon_frontend_build',
                scale: 'small',
                hidden: !responseJson.isRunning,
                xtype: "tbtext",
                id: 'portal-engine-update-frontend-build-running_' + document.id
            });

            document.tab.items.items[0].add({
                text: t('portal-engine.update-frontend-build'),
                iconCls: 'portal_engine_icon_frontend_build',
                scale: 'small',
                hidden: responseJson.isRunning,
                id: 'portal-engine-update-frontend-build-btn_' + document.id,
                handler: function (obj) {

                    Ext.getCmp('portal-engine-update-frontend-build-btn_' + document.id).setHidden(true);
                    Ext.getCmp('portal-engine-update-frontend-build-running_' + document.id).setHidden(false);


                    Ext.Ajax.request({
                        url: Routing.generate('pimcore_portalengine_admin_update_frontend_build'),
                        params: {portalId: document.id},
                        success: function (response) {

                            var responseJson = Ext.util.JSON.decode(response.responseText);
                            if(responseJson.success) {
                                if(responseJson.errors.length) {
                                    Ext.getCmp('portal-engine-update-frontend-build-btn_' + document.id).setHidden(false);
                                    Ext.getCmp('portal-engine-update-frontend-build-running_' + document.id).setHidden(true);
                                    Ext.MessageBox.alert(t("portal-engine_frontend-build-errors"), responseJson.errorsHtml);
                                } else {
                                    Ext.getCmp('portal-engine-update-frontend-build-btn_' + document.id).setHidden(false);
                                    Ext.getCmp('portal-engine-update-frontend-build-running_' + document.id).setHidden(true);

                                    var resultWindow = Ext.create('Ext.Window', {
                                        width: 450,
                                        modal: true,
                                        autoScroll: true,
                                        title: t('portal-engine.frontend-build-finished'),
                                        buttonAlign: "center",
                                        items: [
                                            {
                                                xtype: "displayfield",
                                                value: t('portal-engine.frontend-build-finished.message'),
                                                style: "padding: 20px;",
                                                id: "portal-engine_frontend-build-result_" + document.id
                                            }],
                                        buttons: [
                                            {
                                                text: t("ok"),
                                                iconCls: "pimcore_icon_accept",
                                                handler: function () {
                                                    resultWindow.hide();
                                                    resultWindow.destroy();
                                                }.bind(this)
                                            }]
                                    });

                                    resultWindow.show();
                                }
                            }

                        }.bind(this)
                    });

                }.bind(this, document)
            });

            pimcore.layout.refresh();
        });

    }
});

var PimcorePortalEngineBundlePlugin = new pimcore.plugin.PimcorePortalEngineBundle();
