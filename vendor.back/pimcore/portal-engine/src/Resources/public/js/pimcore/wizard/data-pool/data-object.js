/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.PimcorePortalEngineBundle.wizard.dataPool.DataObject");

pimcore.plugin.PimcorePortalEngineBundle.wizard.dataPool.DataObject = Class.create({

    initialize: function() {
        this.id = 'portal-engine-' + (new Date()).getTime();
    },

    getDataPoolTypeLabel: function() {
        return t('portal-engine.wizard.data-pool.data-object');
    },

    getIconClass: function() {
        return 'pimcore_icon_object';
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
                    value: "object"
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
                     name: "iconPreview",
                     hideLabel: true,
                     width: 30,
                     height: 30,
                     id: "iconPreview-" + this.id,
                     hidden: true,
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
                     fieldLabel: t('portal-engine.wizard.class-definition') + ' <span style="color:red;">*</span>',
                     name: 'classDefinition',
                     labelWidth: 200,
                     allowBlank: false,
                     xtype: "combo",
                     store: this.createSelectStore(Routing.generate('pimcore_portalengine_admin_wizard_get_class_definitions')),
                     displayField: 'name',
                     valueField: 'id',
                     listeners: {
                         change: function(select) {
                             Ext.getCmp('detailPageLayout-' + this.id).setValue(null);
                             Ext.getCmp('detailPageLayout-' + this.id).setStore(
                                 this.createSelectStore(Routing.generate('pimcore_portalengine_admin_wizard_get_object_layouts', {classDefinition:select.getValue()}))
                             );

                             Ext.getCmp('detailPageLayout-' + this.id).store.load();
                         }.bind(this)
                     }
                 },
                 {
                     fieldLabel: t('portal-engine.wizard.detail-page-layout') + ' <span style="color:red;">*</span>',
                     name: 'detailPageLayout',
                     id: 'detailPageLayout-' + this.id,
                     labelWidth: 200,
                     allowBlank: false,
                     xtype: "combo",
                     displayField: 'name',
                     valueField: 'id',
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
                     fieldLabel: t('portal-engine.wizard.available-download-formats'),
                     name: 'availableDownloadFormats',
                     labelWidth: 200,
                     xtype: "tagfield",
                     store: this.createSelectStore(Routing.generate('pimcore_portalengine_admin_wizard_get_available_download_formats')),
                     displayField: 'name',
                     valueField: 'id',
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
                 }

            ]
        });

        return this.formPanel;
    },

    createSelectStore: function(url) {
        return pimcore.plugin.PimcorePortalEngineBundle.wizard.helpers.createSelectStore(url)
    }
});