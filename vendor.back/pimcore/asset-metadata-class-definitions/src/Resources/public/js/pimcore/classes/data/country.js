/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.country");
pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.country = Class.create(pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.data, {

    type: "country",

    getGroup: function () {
        return "select";
    },

});