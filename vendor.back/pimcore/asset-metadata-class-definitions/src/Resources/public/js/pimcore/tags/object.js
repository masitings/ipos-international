/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.tags.object");
pimcore.plugin.asset_metadata_class_definitions.bundle.tags.object = Class.create(pimcore.plugin.asset_metadata_class_definitions.bundle.tags.abstractManyToOneRelation, {

    type: "object",

});

pimcore.plugin.asset_metadata_class_definitions.bundle.tags.object.addMethods(pimcore.plugin.asset_metadata_class_definitions.bundle.edit);