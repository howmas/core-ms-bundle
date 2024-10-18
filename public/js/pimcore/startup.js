pimcore.registerNS("pimcore.plugin.HowMASCoreMSBundle");

pimcore.plugin.HowMASCoreMSBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("HowMASCoreMSBundle ready!");
    }
});

var HowMASCoreMSBundlePlugin = new pimcore.plugin.HowMASCoreMSBundle();
