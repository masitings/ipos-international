/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.plugin.PimcorePortalEngineBundle.collections.getOpenTrees = function () {
    var openTrees = localStorage.getItem("pimcore_portalengine_opentrees");
    if (!openTrees) {
        openTrees = [];
    } else {
        openTrees = JSON.parse(openTrees);
    }

    return openTrees;
};

pimcore.plugin.PimcorePortalEngineBundle.collections.rememberOpenTree = function (item, forceOpenTab) {
    var openTrees = pimcore.plugin.PimcorePortalEngineBundle.collections.getOpenTrees();

    if (!in_array(item, openTrees)) {
        openTrees.push(item);
    }

    // limit to the latest 10
    openTrees.reverse();
    openTrees.splice(10, 1000);
    openTrees.reverse();

    localStorage.setItem("pimcore_portalengine_opentrees", JSON.stringify(openTrees));
};

pimcore.plugin.PimcorePortalEngineBundle.collections.forgetOpenTree = function (item) {

    var openTrees = pimcore.plugin.PimcorePortalEngineBundle.collections.getOpenTrees();

    if (in_array(item, openTrees)) {
        var pos = array_search(item, openTrees);
        openTrees.splice(pos, 1);
    }

    localStorage.setItem("pimcore_portalengine_opentrees", JSON.stringify(openTrees));
};