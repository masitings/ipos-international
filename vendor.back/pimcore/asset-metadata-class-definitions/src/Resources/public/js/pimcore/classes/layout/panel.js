/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.classes.layout.panel");
pimcore.plugin.asset_metadata_class_definitions.bundle.classes.layout.panel = Class.create(pimcore.object.classes.layout.layout, {

    type: "panel",

    initialize: function (treeNode, initData) {
        this.type = "panel";

        this.initData(initData);

        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t("panel");
    },

    getIconClass: function () {
        return "pimcore_icon_panel";
    },

    getLayout: function ($super) {
        $super();

        var layouts = Ext.create('Ext.data.Store', {
            fields: ['abbr', 'name'],
            data : [
                {"abbr":"", "name":"Default"},
                {"abbr":"fit", "name":"Fit"}
            ]
        });

        this.layout.add({
            xtype: "form",
            bodyStyle: "padding: 10px;",
            style: "margin: 10px 0 10px 0",
            items: [
                {
                    xtype: "combo",
                    fieldLabel: t("layout"),
                    name: "layout",
                    value: this.datax.layout,
                    store: layouts,
                    triggerAction: 'all',
                    editable: false,
                    displayField: 'name',
                    valueField: 'abbr',
                },{
                    xtype: "checkbox",
                    fieldLabel: t("border"),
                    name: "border",
                    checked: this.datax.border,
                },{
                    xtype: "numberfield",
                    name: "labelWidth",
                    fieldLabel: t("label_width"),
                    value: this.datax.labelWidth
                }, this.getIconFormElement()
            ]
        });

        return this.layout;
    }
});