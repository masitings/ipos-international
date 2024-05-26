/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.calculatedValue");
pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.calculatedValue = Class.create(pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.data, {

    type: "calculatedValue",

    getGroup: function () {
        return "other";
    },

    getSpecificPanelItems: function (datax) {
        specificItems = [
            {
                xtype: 'textfield',
                width: 600,
                fieldLabel: t("calculatedValue_calculatorclass"),
                labelWidth: 140,
                name: 'calculatorClass',
                value: this.datax.calculatorClass
            },
            {
                xtype: "displayfield",
                hideLabel: true,
                value: t('asset_metadata_class_definitions_bundle-calculatedValue_explanation'),
                cls: "pimcore_extra_label_bottom",
                style: "color:red; font-weight: bold; padding-bottom:0;"
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
                    calculatorClass: source.datax.calculatorClass
                });
        }
    },
});