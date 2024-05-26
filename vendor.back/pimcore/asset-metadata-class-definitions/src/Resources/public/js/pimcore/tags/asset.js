/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.tags.asset");
pimcore.plugin.asset_metadata_class_definitions.bundle.tags.asset = Class.create(pimcore.plugin.asset_metadata_class_definitions.bundle.tags.abstractManyToOneRelation, {

    type: "asset",

});

pimcore.plugin.asset_metadata_class_definitions.bundle.tags.asset.addMethods(pimcore.plugin.asset_metadata_class_definitions.bundle.edit);