/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.localizedfields");
pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.localizedfields = Class.create(pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.data, {

    type: "localizedfields",

    allowIn: {
        asset: true,
        localizedfield: false,
    },

    getLayout: function ($super) {
        var standardSettings = [
            {
                xtype: "textfield",
                fieldLabel: t("name"),
                name: "name",
                width: 540,
                maxLength: 70,
                disabled: true,
                value: this.datax.name
            },
            {
                xtype: "textfield",
                fieldLabel: t("title") + " (" + t("label") + ")",
                name: "title",
                itemId: "title",
                width: 540,
                value: this.datax.title,
                enableKeyEvents: true,
                listeners: {
                    keyup: function (el) {
                        el["_autooverwrite"] = false;
                    },
                    afterrender: function (el) {
                        if(el.getValue().length < 1) {
                            el["_autooverwrite"] = true;
                        }
                    }
                }
            }
        ];

        this.standardSettingsForm = new Ext.form.FormPanel(
            {
                bodyStyle: "padding: 10px;",
                style: "margin: 0 0 10px 0",
                defaults: {
                    labelWidth: 140
                },
                itemId: "standardSettings",
                items: standardSettings
            }
        );

        var layoutSettings = [
            {
                xtype: "textfield",
                fieldLabel: t("css_style") + " (float: left; margin:10px; ...)",
                name: "style",
                itemId: "style",
                value: this.datax.style,
                width: 740,
                disabled: !in_array("style",this.availableSettingsFields)
            }
        ];

        this.layoutSettingsForm = new Ext.form.FormPanel(
            {
                title: t("layout_settings"),
                bodyStyle: "padding: 10px;",
                style: "margin: 10px 0 10px 0",
                defaults: {
                    labelWidth: 230
                },
                items: layoutSettings
            }
        );

        this.specificPanel = new Ext.form.FormPanel({
            title: t("specific_settings"),
            bodyStyle: "padding: 10px;",
            style: "margin: 10px 0 10px 0",
            items: [],
            defaults: {
                labelWidth: 140
            }
        });

        var specificItems = this.getSpecificPanelItems(this.datax);
        this.specificPanel.add(specificItems);

        var niceName = (this.getTypeName() ? this.getTypeName() : t(this.getType()));

        this.layout = new Ext.Panel({
            title: '<b>' + this.datax.name + " (" + t("type") + ": " + niceName + ")</b>",
            bodyStyle: 'padding: 10px;',
            items: [
                this.standardSettingsForm,
                this.layoutSettingsForm,
                this.specificPanel
            ]
        });

        this.layout.on("render", this.layoutRendered.bind(this));

        return this.layout;
    },

    getGroup: function () {
        return "structured";
    },

});
