pimcore.registerNS("pimcore.plugin.EnterpriseSubscriptionToolsBundle");

pimcore.plugin.EnterpriseSubscriptionToolsBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.EnterpriseSubscriptionToolsBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);

        this.navEl = Ext.get('pimcore_notification').insertSibling('' +
            '<div id="pimcore_enterprise_subscription" data-menu-tooltip="' + t('bundle_enterprise_subscription') + '" class="pimcore_icon_comments"><div class="icon" />'
            , 'before');
        pimcore.layout.toolbar.prototype.licenseChecker = this.menu;
    },

    pimcoreReady: function (params, broker) {
        window.setTimeout(this.checkLicense.bind(this), 5000);
    },


    checkLicense: function() {

        Ext.Ajax.request({
            url: "/admin/enterprise-subscription-tools/instance-info",
            method: "get",
            success: function (response) {
                var rdata = Ext.decode(response.responseText);
                var request = new XMLHttpRequest();

                request.open('POST', "https://license.pimcore.com/pimcore-license/check", true);

                var data = new FormData();
                data.append('instanceId', rdata.instanceId);
                data.append('instanceCode', rdata.instanceCode);
                data.append('environment', rdata.environment);
                data.append('main_domain', pimcore.settings.main_domain);
                data.append('current_domain', window.location.hostname);

                request.addEventListener('loadend', function(request, event) {
                    if (request.status >= 200 && request.status < 300) {
                        var response = Ext.decode(request.responseText);

                        this.navEl.show();
                        if(response.licenseValid) {
                            this.navEl.addCls('green');
                        }
                        if(response.isDeveloperPackage) {
                            this.navEl.addCls('developer');
                        }

                        this.navEl.on("mousedown", function() {
                            window.open("https://license.pimcore.com/pimcore-license/info?instanceCode=" + rdata.instanceCode);
                        });

                    } else {
                        console.warn(request.statusText, request.responseText);
                    }

                }.bind(this, request));

                request.send(data);

            }.bind(this)
        });
    }
});

var EnterpriseSubscriptionToolsBundle = new pimcore.plugin.EnterpriseSubscriptionToolsBundle();
