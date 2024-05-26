<?php

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */

namespace Pimcore\Bundle\PortalEngineBundle\Event\Asset\Upload;

/**
 * Fires after the asset got saved within the upload process.
 * Can be used to add user messages within the upload process (either for the whole upload or related to the concrete asset).
 * Additionally it is possible to cancel the upload and do a rollback.
 *
 * @example  $postAssetCreateEvent->addGlobalMessage('my-global-message-from-post-asset-create-event');
 * @example  $postAssetCreateEvent->setAssetListEntryMessage('my-entry-message-from-post-asset-create-event');
 * @example  $postAssetCreateEvent->setCancelCurrentUpload(true); Cancel current asset only.
 * @example  $postAssetCreateEvent->setCancelWholeUpload(true); Cancel whole upload.
 */
class PostAssetCreateEvent extends BasicAssetCreateEvent
{
}
