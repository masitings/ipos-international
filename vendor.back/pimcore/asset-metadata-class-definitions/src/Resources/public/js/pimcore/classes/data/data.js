/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.data");
pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data.data = Class.create({

    allowIn: {
        asset: true,
        localizedfield: true,
    },

    initialize: function (treeNode, initData) {
        this.initData(initData);

        this.treeNode = treeNode;
    },


    initData: function (d) {
        this.datax = {
            name: "",
            datatype: "data",
            fieldtype: this.getType()
        };

        Ext.apply(this.datax, d);

        // per default all settings are available
        this.availableSettingsFields = ["name", "title", "style"];
    },

    getSpecificPanelItems: function (datax) {
        var specificItems = [
            {
                xtype: "numberfield",
                fieldLabel: t("width"),
                name: "width",
                value: datax.width
            }
        ];

        return specificItems;
    },

    getLayout: function ($super) {
        this.mandatoryCheckbox = new Ext.form.field.Checkbox({
            fieldLabel: t("mandatoryfield"),
            name: "mandatory",
            itemId: "mandatory",
            checked: this.datax.mandatory
        });

        var standardSettings = [
            {
                xtype: "textfield",
                fieldLabel: t("name"),
                name: "name",
                width: 540,
                maxLength: 70,
                itemId: "name",
                autoCreate: {tag: 'input', type: 'text', maxlength: '70', autocomplete: 'off'},
                enableKeyEvents: true,
                value: this.datax.name,
                listeners: {
                    keyup: function (el) {
                        // autofill title field if untouched and empty
                        var title = el.ownerCt.getComponent("title");
                        if (title["_autooverwrite"] === true) {
                            el.ownerCt.getComponent("title").setValue(el.getValue());
                        }
                    }
                }
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
            },
            this.mandatoryCheckbox
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

    layoutRendered: function (layout) {

        var items = this.layout.queryBy(function() {
            return true;
        });

        for (let i = 0; i < items.length; i++) {
            if (items[i].name == "name") {
                items[i].on("keyup", this.updateName.bind(this));
                break;
            }
        }
    },

    getType: function () {
        return this.type;
    },

    applyData: function () {

        if (!this.layout) {
            return;
        }

        var items = this.layout.queryBy(function() {
            return true;
        });

        for (let i = 0; i < items.length; i++) {
            if (typeof items[i].getValue == "function") {
                this.datax[items[i].name] = items[i].getValue();
            }
        }

        this.datax.fieldtype = this.getType();
        this.datax.datatype = "data";
    },

    getIconClass: function () {
        return "pimcore_icon_" + this.getType();
    },

    getTypeName: function () {
        return t(this.getType());
    },

    updateName: function () {

        var items = this.layout.queryBy(function() {
            return true;
        });

        if (this.treeNode) {
            for (let i = 0; i < items.length; i++) {
                if (items[i].name == "name") {
                    this.treeNode.set("text", items[i].getValue());
                    break;
                }
            }
        }
    },

    getData: function () {
        return this.datax;
    },

    applySpecialData: function(source) {

    },

    getGroup: function () {
        // type will not be grouped
        return "";
    },
});
