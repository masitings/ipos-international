/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.startup");

pimcore.plugin.asset_metadata_class_definitions.bundle.startup = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.asset_metadata_class_definitions.bundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        if (pimcore.globalmanager.get("user").isAllowed("classes")) {
            let settingsMenu = pimcore.globalmanager.get("layout_toolbar").settingsMenu;
            if (settingsMenu) {
                settingsMenu.insert(settingsMenu.items.length + 1, {
                    text: t('asset_metadata_class_definitions_bundle-toolbar_menu'),
                    iconCls: "plugin_pimcore_assetmetadataclassdefinitions_icon",
                    cls: "pimcore_main_menu",
                    handler: this.openDefinitions.bind(this)
                });
                settingsMenu.updateLayout();
            }
        }
    },

    openDefinitions: function () {
        try {
            pimcore.globalmanager.get("asset_metadata_class_definitions_panel").activate();
        } catch (e) {
            pimcore.globalmanager.add("asset_metadata_class_definitions_panel", new pimcore.plugin.asset_metadata_class_definitions.bundle.configurationTree());
        }
    },

    preCreateMenuOption: function (eventData) {
        if (eventData.key == "settings.predefinedMetadata") {
            eventData.isAllowed = false;
        }
    },

    prepareAssetMetadataGridConfigurator: function (eventData) {
        eventData.implementation = pimcore.asset.helpers.gridConfigDialog;
        eventData.additionalConfig = {
            treeUrl:"/admin/asset-metadata-classdefinitions-bundle/backend/get-metadata-for-column-config"
        };
    },

    preCreateAssetMetadataEditor: function (editor, eventParams) {
        let instance = new pimcore.plugin.asset_metadata_class_definitions.bundle.editorPanel(
            {
                asset: eventParams.asset,
                dataProvider: eventParams.dataProvider
            });
        eventParams.instance = instance;
    }
});

var assetmetadataClassDefinitionsBundle = new pimcore.plugin.asset_metadata_class_definitions.bundle.startup();

