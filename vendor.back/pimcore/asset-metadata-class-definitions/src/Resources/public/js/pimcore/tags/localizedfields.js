/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.tags.localizedfields");
pimcore.plugin.asset_metadata_class_definitions.bundle.tags.localizedfields = Class.create(pimcore.object.tags.localizedfields, {
    isSplitViewEnabled: function () {
        return false;
    },

    initialize: function ($super, data, fieldConfig) {
        this.registeredListenerKeys = [];

        $super(data, fieldConfig);
    },

    getDataProvider: function (currentLanguage) {


        var dataProvider = {
            getDataForField: function (currentLanguage, fieldConfig) {
                let name = fieldConfig.name;
                let metadataProvider = this.context.metadataProvider;
                let configurationName = this.context.configurationName;
                let configuration = this.context.configurations[configurationName];
                let prefix = configuration['prefix'];
                let value = metadataProvider.getItemData(prefix, name, currentLanguage);
                return value;

            }.bind(this, currentLanguage),

            addToDataFields: function (currentLanguage, field, name) {
                this.languageElements[currentLanguage].push(field);
            }.bind(this, currentLanguage),

            registerChangeListener: function(configurationName, currentLanguage, field) {
                let configuration = this.context.configurations[configurationName];
                let prefix = configuration['prefix'];
                let key = this.context.metadataProvider.buildKey(prefix, field.name, currentLanguage);
                this.registeredListenerKeys.push({
                    key: key,
                    targetId: field.getTargetId()
                });
                this.context.metadataProvider.registerChangeListener(key, field.getTargetId(),
                    function(field, eventType, name, language, value, type, originator) {
                        value  = pimcore.asset.metadata.tags[field.fieldConfig.fieldtype].prototype.unmarshal(value);
                        field.processMetadataChange(eventType, name, language, value, type, originator);
                    }.bind(this, field)
                );

                field.registerChangeListener({
                    callback: this.componentValueChanged.bind(this, currentLanguage)
                });
            }.bind(this, this.context.configurationName, currentLanguage)
        };

        return dataProvider;
    },

    componentValueChanged: function(language, changeContext, fieldname, type, newValue, config, originator) {

        let configuration = this.context.configurations[this.context.configurationName];
        let prefix = configuration['prefix'];

        let key = this.context.metadataProvider.buildKey(this.context.configurationName, fieldname, language);
        newValue =  pimcore.asset.metadata.tags[type].prototype.marshal(newValue);

        this.context.metadataProvider.update({
            name: prefix + "." + fieldname,
            language: language,
            type: type,
            config: config
        }, newValue, originator)
    },

    setValue: function(newValue) {
        this.component.setValue(newValue);
    },

    getValue: function() {
        return this.component.getValue();
    },

    getAdditionalGridConfig: function() {
        return null;
    }
});

pimcore.plugin.asset_metadata_class_definitions.bundle.tags.localizedfields.addMethods(pimcore.plugin.asset_metadata_class_definitions.bundle.layoutHelper);
pimcore.plugin.asset_metadata_class_definitions.bundle.tags.localizedfields.addMethods(pimcore.plugin.asset_metadata_class_definitions.bundle.edit);
