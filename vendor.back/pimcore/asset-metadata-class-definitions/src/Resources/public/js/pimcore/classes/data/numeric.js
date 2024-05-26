/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.numeric");
pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.numeric = Class.create(pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.data, {

    type: "numeric",

    getGroup: function () {
        return "text_and_number";
    },

    getSpecificPanelItems: function (datax) {

        let specificItems = [
            {
                xtype: "checkbox",
                fieldLabel: t("integer"),
                name: "integer",
                checked: datax.integer
            }, {
                xtype: "checkbox",
                fieldLabel: t("only_unsigned"),
                name: "unsigned",
                checked: datax["unsigned"]
            }, {
                xtype: "numberfield",
                fieldLabel: t("min_value"),
                name: "minValue",
                value: datax.minValue
            }, {
                xtype: "numberfield",
                fieldLabel: t("max_value"),
                name: "maxValue",
                value: datax.maxValue
            }
        ];

        return specificItems;
    },


    applyData: function ($super) {
        $super();
    },


    applySpecialData: function (source) {
        if (source.datax) {
            if (!this.datax) {
                this.datax = {};
            }
            Ext.apply(this.datax,
                {
                    width: source.datax.width,
                    integer: source.datax.integer,
                    unsigned: source.datax.unsigned,
                    minValue: source.datax.minValue,
                    maxValue: source.datax.maxValue
                });
        }
    },
});