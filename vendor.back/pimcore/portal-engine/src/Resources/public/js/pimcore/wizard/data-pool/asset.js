/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.PimcorePortalEngineBundle.wizard.dataPool.Asset");

pimcore.plugin.PimcorePortalEngineBundle.wizard.dataPool.Asset = Class.create({

    getDataPoolTypeLabel: function() {
        return t('portal-engine.wizard.data-pool.asset');
    },


    getIconClass: function() {
        return 'pimcore_icon_asset';
    },

    getPanel: function() {
        this.formPanel = Ext.create('Ext.form.Panel', {
            title: this.getDataPoolTypeLabel(),
            bodyPadding: 10,
            defaultType: 'textfield',
            iconCls: this.getIconClass(),
            closable: true,
            items: [
                {
                    xtype: "hidden",
                    name: "type",
                    value: "asset"
                },
                {
                    fieldLabel: t('portal-engine.wizard.data-pool-name') + ' <span style="color:red;">*</span>',
                    name: 'dataPoolName',
                    labelWidth: 200,
                    allowBlank: false,
                    listeners: {
                        change: function(input) {
                            this.formPanel.setTitle(input.getValue() ? input.getValue() : this.getDataPoolTypeLabel());
                        }.bind(this)
                    }
                },
                {
                    xtype: "displayfield",
                    hideLabel: true,
                    width: 30,
                    height: 30,
                    id: "iconPreview-" + this.id,
                    hidden: true,
                    name: "iconPreview",
                    cls: "portal_engine_wizard_icon_preview"
                },
                {
                    fieldLabel: t('portal-engine.wizard.icon'),
                    name: 'icon',
                    labelWidth: 200,
                    xtype: "combo",
                    width: 30,
                    store: this.createSelectStore(Routing.generate('pimcore_portalengine_admin_wizard_get_icons')),
                    displayField: 'name',
                    valueField: 'id',
                    listeners: {
                        change: function(select) {
                            Ext.getCmp("iconPreview-" + this.id).setValue(select.getRawValue());
                            Ext.getCmp("iconPreview-" + this.id).setHidden(select.getValue() === null);
                        }.bind(this)
                    }
                },
                {
                    fieldLabel: t('portal-engine.wizard.enable-folder-navigation'),
                    name: 'enableFolderNavigation',
                    labelWidth: 200,
                    xtype: "checkbox"
                },
                {
                    fieldLabel: t('portal-engine.wizard.enable-tag-navigation'),
                    name: 'enableTagNavigation',
                    labelWidth: 200,
                    xtype: "checkbox"
                },
                {
                    fieldLabel: t('portal-engine.wizard.available-download-thumbnails'),
                    name: 'availableDownloadThumbnails',
                    labelWidth: 200,
                    xtype: "tagfield",
                    store: this.createSelectStore(Routing.generate('pimcore_portalengine_admin_wizard_get_available_download_thumbnails')),
                    displayField: 'name',
                    valueField: 'id',
                },
                {
                    fieldLabel: t('portal-engine.wizard.direct-download-shortcuts'),
                    name: 'directDownloadShortcuts',
                    labelWidth: 200,
                    xtype: "tagfield",
                    store: this.createSelectStore(Routing.generate('pimcore_portalengine_admin_wizard_get_available_download_thumbnails')),
                    displayField: 'name',
                    valueField: 'id'
                },
                {
                    fieldLabel: t('portal-engine.wizard.available-download-formats'),
                    name: 'availableDownloadFormats',
                    labelWidth: 200,
                    xtype: "tagfield",
                    store: this.createSelectStore(Routing.generate('pimcore_portalengine_admin_wizard_get_available_download_formats')),
                    displayField: 'name',
                    valueField: 'id',
                    tooltip: 'tooltip'
                },
                {
                    fieldLabel: t('portal-engine.wizard.visible-languages'),
                    name: 'visibleLanguages',
                    labelWidth: 200,
                    xtype: "tagfield",
                    store: pimcore.plugin.PimcorePortalEngineBundle.wizard.helpers.getLanguagesStore(),
                    queryMode: 'local',
                    displayField: "value",
                    valueField: "key",
                },
                {
                    fieldLabel: t('portal-engine.wizard.editable-languages'),
                    name: 'editableLanguages',
                    labelWidth: 200,
                    xtype: "tagfield",
                    store: pimcore.plugin.PimcorePortalEngineBundle.wizard.helpers.getLanguagesStore(),
                    queryMode: 'local',
                    displayField: "value",
                    valueField: "key",
                }

            ]
        });

        return this.formPanel;
    },

    createSelectStore: function(url) {
        return pimcore.plugin.PimcorePortalEngineBundle.wizard.helpers.createSelectStore(url)
    }
});