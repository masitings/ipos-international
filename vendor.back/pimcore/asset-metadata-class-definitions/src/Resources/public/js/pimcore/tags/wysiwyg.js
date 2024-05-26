/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.tags.wysiwyg");
pimcore.plugin.asset_metadata_class_definitions.bundle.tags.wysiwyg = Class.create(pimcore.object.tags.wysiwyg, {
    initCkEditor: function ($super) {
        $super();
        this.ckeditor.on("change", this.changeListener);
    },

    setValue: function(newValue, originator) {
        this.ckeditor.setData(newValue);
    },

    getAdditionalGridConfig: function() {
        return null;
    }
});

pimcore.plugin.asset_metadata_class_definitions.bundle.tags.wysiwyg.addMethods(pimcore.plugin.asset_metadata_class_definitions.bundle.edit);