/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from "react";
import CollectionList from "~portal-engine/scripts/features/collections/components/CollectionList";
import AddCollectionButton from "~portal-engine/scripts/features/collections/components/AddCollectionButton";
import CreatePublicShareModal from "~portal-engine/scripts/features/public-share/components/CreatePublicShareModal";
import Trans from "~portal-engine/scripts/components/Trans";

export function CollectionOverview() {
    return (
        <div className="main-content__main">
            <div className="container">

                <div className="mb-3 float-md-right text-right">
                    <AddCollectionButton/>
                </div>

                <h1><Trans t="collection.list.headline"/></h1>

                <CollectionList/>

                <CreatePublicShareModal />
            </div>
        </div>
    );
}