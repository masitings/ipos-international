/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.editorPanel");
pimcore.plugin.asset_metadata_class_definitions.bundle.editorPanel = Class.create({

    initialize: function (config) {
        this.addedTypes = {};
        this.currentElements = {};
        this.blockComponents = {};

        this.config = config.dataProvider;
        this.asset = config.asset;
        this.dataProvider = config.dataProvider;

        this.standardGrid = new pimcore.asset.metadata.grid({
            asset: this.asset,
            dataProvider: this.dataProvider,
            iconCls: "pimcore_icon_metadata",
            hideAddPredefinedButton: true
            // ,
            // disableName: true,
            // disableLanguage: true
        });
    },

    getLayout: function () {
        this.dataProvider.setStore(this.asset.data.metadata);

        this.loadDefinitions();

        let standardGridLayout = this.standardGrid.getLayout();

        let panelConf = {
            autoHeight: true,
            activeTab: 0,
            items: [standardGridLayout]
        };

        this.tabpanel = new Ext.TabPanel(panelConf);

        panelConf = {
            title: t("custom_metadata"),
            autoHeight: true,
            autoScroll:true,
            border: true,
            style: "margin-bottom: 10px",
            iconCls: "pimcore_material_icon_metadata pimcore_material_icon",
            componentCls: "object_field",
            items: [this.tabpanel]
        };

        this.component = new Ext.Panel(panelConf);
        return this.component;
    },


    getValues: function () {
        if (!this.isInitialized) {
            throw "not initialized";
        }
        let values =  this.dataProvider.getSubmitValues();
        let result = {
            values: values,
            collections: Object.keys(this.currentElements)
        };

        return result;
    },

    loadDefinitions: function () {

        Ext.Ajax.request({
            url: "/admin/asset-metadata-classdefinitions-bundle/backend/list-configurations",
            params: {
                forEditor: 1
            },
            success: this.initData.bind(this)
        });
    },

    initData: function (response) {
        let responseData = Ext.decode(response.responseText);
        this.configurations = responseData.configurations;

        this.component.insert(0, this.getControls());

        let activeDefinitions = this.asset.data.asset_metadata_class_definitions_bundle_activeDefinitions;

        if (activeDefinitions && activeDefinitions.length > 0) {
            for (let i = 0; i < activeDefinitions.length; i++) {
                this.addBlockElement(activeDefinitions[i], true, false);
            }
        }

        this.tabpanel.setActiveTab(0);

        pimcore.layout.refresh();
        this.isInitialized = true;
    },

    buildMenu: function (data, blockElement) {
        var menu = [];

        if (data) {
            for (let key in data) {
                if (data.hasOwnProperty(key)) {

                    let elementData = data[key];
                    if (this.addedTypes[elementData.name]) {
                        continue;
                    }

                    let menuItem = {
                        text: elementData.title ? t(elementData.title) : t(elementData.name)
                    };

                    if (elementData.icon) {
                        menuItem["icon"] = elementData.icon;
                    } else {
                        menuItem["iconCls"] = "pimcore_icon_panel";
                    }

                    menuItem.handler = this.addBlock.bind(this, elementData.name);

                    menu.push(menuItem);
                }
            }
        }
        return menu;
    },

    getControls: function () {
        let menu = this.buildMenu(this.configurations);
        let items = [];

        if (menu.length == 1) {
            if (!menu[0].menu) {
                var handler = menu[0].menu ? menu[0].menu[0].handler : menu[0].handler;
                items.push({
                    cls: "pimcore_block_button_plus",
                    iconCls: "pimcore_icon_plus",
                    handler: handler
                });
            } else {
                items.push({
                    cls: "pimcore_block_button_plus",
                    iconCls: "pimcore_icon_plus",
                    menu: menu
                });

            }
        } else if (menu.length > 1) {
            items.push({
                cls: "pimcore_block_button_plus",
                iconCls: "pimcore_icon_plus",
                menu: menu
            });
        }

        if (items.length > 0) {
            items.push({
                xtype: "tbtext",
                text: t("asset_metadata_class_definitions_bundle-add_set")
            });
        }

        var toolbar = new Ext.Toolbar({
            items: items
        });

        return toolbar;
    },

    addBlock: function (type) {
        this.addBlockElement(
            type,
            true,
            true);
    },

    removeBlock: function (blockElement) {

        Ext.MessageBox.confirm(' ', t('delete_message'), function (blockElement, answer) {
            if (answer == "yes") {

                let blockData = this.currentElements[blockElement.fieldtype];
                let registeredListenerKeys = blockData['registeredListenerKeys'];
                let fields = blockData['fields'];
                let configurationName = blockData['configurationName'];
                let configuration = this.configurations[configurationName];
                let prefix = configuration['prefix'];

                for (let i = 0; i < registeredListenerKeys.length; i++) {
                    let item = registeredListenerKeys[i];
                    let key = item['key'];
                    let targetId = item['targetId'];
                    this.dataProvider.unregisterChangeListener(key, targetId);
                }

                let dataItems = this.dataProvider.getDataAsArray();
                for (let i = 0; i < dataItems.length; i++) {
                    let dataItem =  dataItems[i];
                    let name = dataItem["name"];
                    if (name.startsWith(prefix)) {
                        this.dataProvider.remove(dataItem, null);
                    }
                }

                this.tabpanel.remove(blockElement);
                this.addedTypes[blockElement.fieldtype] = false;
                delete this.currentElements[blockElement.fieldtype];
                this.component.remove(this.component.getComponent(0));
                this.component.insert(0, this.getControls());
                this.component.updateLayout();


                this.dirty = true;
            }
        }.bind(this, blockElement), this);
        return false;
    },

    componentValueChanged: function(configurationName, changeContext, fieldname, type, newValue, config, originator) {
        if (typeof pimcore.asset.metadata.tags[type] != "undefined") {
            newValue = pimcore.asset.metadata.tags[type].prototype.marshal(newValue);
        }

        let configuration = this.configurations[configurationName];
        let prefix = configuration['prefix'];
        this.dataProvider.update({
            name: prefix + "." + fieldname,
            language: null,
            type: type,
            config: config
        }, newValue, originator)
    },

    addBlockElement: function (configurationName, ignoreChange, manuallyAdded) {
        if (!configurationName) {
            return;
        }
        if (!this.configurations[configurationName]) {
            return;
        }

        let configuration = this.configurations[configurationName];
        let dataFields = [];
        let currentData = {};
        let registeredListenerKeys = [];

        var elementDataProvider = {
            getDataForField: function (configurationName, currentData, field, context) {
                let configuration = this.configurations[configurationName];
                let prefix = configuration['prefix'];
                let value = this.dataProvider.getItemData(prefix,field.name, null);
                return value;
            }.bind(this, configurationName, currentData),

            addToDataFields: function (dataFields, field, name, context) {
                name = field.name;
                dataFields.push(field);
            }.bind(this, dataFields),

            registerChangeListener: function(configurationName, registeredListenerKeys, field) {
                let configuration = this.configurations[configurationName];
                let prefix = configuration['prefix'];
                let key = this.dataProvider.buildKey(prefix, field.name, "");
                registeredListenerKeys.push({
                    key: key,
                    targetId: field.getTargetId()
                });
                this.blockComponents[key] = this.blockComponents[key] || {};
                this.blockComponents[key][configurationName] = field;
                this.dataProvider.registerChangeListener(key, field.getTargetId(),
                    function(field, eventType, name, language, value, type, originator) {
                        if (typeof pimcore.asset.metadata.tags[field.fieldConfig.fieldtype] != "undefined") {
                            value = pimcore.asset.metadata.tags[field.fieldConfig.fieldtype].prototype.unmarshal(value);
                        }
                        field.processMetadataChange(eventType, name, language, value, type, originator);
                    }.bind(this, field)
                );

                field.registerChangeListener({
                    callback: this.componentValueChanged.bind(this, configurationName)
                });
            }.bind(this, configurationName, registeredListenerKeys)
        };

        var title = configuration.title;
        var childConfig = configuration.layoutDefinitions;

        var blockElement = new Ext.Panel({
            style: "margin: 0 0 10px 0;",
            cls: 'pimcore_objectbrick_item',
            closable: true,
            autoHeight: true,
            border: false,
            title: title ? t(title) : t(configurationName),
            icon: configuration.icon,
            items: [],
            listeners: {
                afterrender: function (childConfig, elementDataProvider, manuallyAdded, panel) {
                    if (!panel.__tabpanel_initialized) {
                        var copy = Ext.decode(Ext.encode(childConfig));
                        var children = this.getRecursiveLayout(copy, null, {
                            containerType: "block",
                            metadataProvider: this.dataProvider,
                            configurationName: configurationName,
                            configurations: this.configurations,
                            assetId: this.asset.id
                        }, false, true, elementDataProvider);

                        if (children) {
                            panel.add(children);
                        }

                        panel.updateLayout();

                        if (panel.setActiveTab) {
                            var activeTab = panel.items.items[0];
                            if (activeTab) {
                                activeTab.updateLayout();
                                panel.setActiveTab(activeTab);
                            }
                        }

                        panel.__tabpanel_initialized = true;
                    }
                }.bind(this, childConfig, elementDataProvider, manuallyAdded)

            }
        });


        blockElement.on("beforeclose", this.removeBlock.bind(this, blockElement));

        this.component.remove(this.component.getComponent(0));

        this.addedTypes[configurationName] = true;

        blockElement.key = configurationName;
        blockElement.fieldtype = configurationName;

        let keys = Object.keys(this.currentElements);
        this.tabpanel.insert(keys.length, blockElement);
        this.component.insert(0, this.getControls());

        if (manuallyAdded) {
            this.tabpanel.setActiveTab(keys.length);
        }

        this.tabpanel.updateLayout();
        this.component.updateLayout();

        this.currentElements[configurationName] = {
            container: blockElement,
            fields: dataFields,
            type: configurationName,
            registeredListenerKeys: registeredListenerKeys,
            configurationName: configurationName
        };

        if (!ignoreChange) {
            this.dirty = true;
            this.tabpanel.setActiveTab(blockElement);
        }
    }
});

pimcore.plugin.asset_metadata_class_definitions.bundle.editorPanel.addMethods(pimcore.plugin.asset_metadata_class_definitions.bundle.layoutHelper);