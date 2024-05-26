/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.PimcorePortalEngineBundle.wizard.helpers.x");

pimcore.plugin.PimcorePortalEngineBundle.wizard.helpers.getLanguagesStore = function() {
    var data = [];
    for (var i = 0; i < pimcore.settings.websiteLanguages.length; i++) {
        var language = pimcore.settings.websiteLanguages[i];
        data.push([language, t(pimcore.available_languages[language])]);
    }

    return new Ext.data.ArrayStore({
            fields: ["key", "value"],
            data: data
        }
    );
};

pimcore.plugin.PimcorePortalEngineBundle.wizard.helpers.createSelectStore = function(url) {
    return new Ext.data.Store({
        autoDestroy: true,
        proxy: {
            type: 'ajax',
            url: url,
            reader: {
                type: 'json',
                rootProperty: 'data'
            }
        },
        fields: ["id", "name"]
    })
};