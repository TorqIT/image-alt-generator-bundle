pimcore.registerNS("pimcore.plugin.ImageAltGeneratorBundle");

pimcore.plugin.ImageAltGeneratorBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.ImageAltGeneratorBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("ImageAltGeneratorBundle ready!");
    }
});

var ImageAltGeneratorBundlePlugin = new pimcore.plugin.ImageAltGeneratorBundle();
