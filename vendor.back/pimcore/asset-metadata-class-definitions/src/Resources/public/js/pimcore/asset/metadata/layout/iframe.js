/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.asset.metadata.layout.iframe");
pimcore.plugin.asset_metadata_class_definitions.bundle.asset.metadata.layout.iframe = Class.create(pimcore.object.layout.iframe, {

    getLayout: function () {

        var queryString = Ext.Object.toQueryString({
            renderingData: this.config.renderingData,
            name: this.config.name
        });

        var html = '<iframe src="' + this.config.iframeUrl + "?" + queryString + '"frameborder="0" width="100%" height="' + (this.config.height - 38) + '" style="display: block"></iframe>';

        let addButton = false;

        let items = [
            {
                xtype: "tbtext",
                text: this.config.title
            }, "->",
            {
                xtype: 'button',
                text: t('refresh'),
                iconCls: 'pimcore_icon_reload',
                handler: function () {
                    var key = "asset_" + this.context.assetId;

                    if (pimcore.globalmanager.exists(key)) {
                        var assetTab = pimcore.globalmanager.get(key);
                        assetTab.saveToSession(function () {
                                               this.component.setHtml(html);
                        }.bind(this));
                        this.component.setHtml(html);
                    }
                }.bind(this)
            }
        ];

        this.component = new Ext.Panel({
            border: true,
            style: "margin-bottom: 10px",
            cls: "pimcore_layout_iframe_border",
            height: this.config.height,
            width: this.config.width,
            scrollable: true,
            html: html,
            tbar: {
                items: items
            }
        });
        return this.component;

    }


});
