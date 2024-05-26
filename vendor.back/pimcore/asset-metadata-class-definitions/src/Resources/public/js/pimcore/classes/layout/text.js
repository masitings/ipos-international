/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.classes.layout.text");
pimcore.plugin.asset_metadata_class_definitions.bundle.classes.layout.text = Class.create(pimcore.object.classes.layout.layout, {

    type: "text",

    initialize: function (treeNode, initData) {
        this.type = "text";

        this.initData(initData);

        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t("text");
    },

    getIconClass: function () {
        return "pimcore_icon_text";
    },

    getLayout: function ($super) {
        $super();

        this.layout.add({
            xtype: "form",
            title: t("specific_settings"),
            bodyStyle: "padding: 10px;",
            style: "margin: 10px 0 10px 0",
            items: [
                {
                    xtype: "checkbox",
                    fieldLabel: t("border"),
                    name: "border",
                    checked: this.datax.border,
                },
                {
                    xtype: 'container',
                    style: 'padding-top:10px;',
                    html: 'You can use the following markup (in source edit mode) to make custom alerts: <br> <pre>&lt;div class=&quot;alert alert-success&quot;&gt;Your Message&lt;/div&gt;</pre>The following contextual classes are available: <pre>alert-primary, alert-secondary, alert-success, alert-danger, alert-warning, alert-info</pre>'
                },
                {
                    xtype: "htmleditor",
                    cls: 'objectlayout_element_text',
                    height: 300,
                    value: this.datax.html,
                    name: "html",
                    enableSourceEdit: true,
                    enableFont: false,
                    listeners: {
                        initialize: function (el) {
                            var head = el.getDoc().head;
                            var link = document.createElement("link");

                            link.type = "text/css";
                            link.rel = "stylesheet";
                            link.href = '/bundles/pimcoreadmin/css/admin.css';

                            head.appendChild(link);
                            el.getEditorBody().classList.add('objectlayout_element_text');
                        }
                    }
                }
            ]
        });

        return this.layout;
    }
});