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

use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\Asset\Upload\AssetUploadList;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires after the a whole upload process (potentially multiple files) got finished.
 * Can be used to add a global user message.
 *
 * @example  $postUploadEvent->getAssetUploadList()->addMessage('my-message-from-event');
 */
class PostUploadEvent extends Event
{
    /** @var AssetUploadList */
    protected $assetUploadList;

    /**
     * PostUploadEvent constructor.
     *
     * @param AssetUploadList $assetUploadList
     */
    public function __construct(AssetUploadList $assetUploadList)
    {
        $this->assetUploadList = $assetUploadList;
    }

    /**
     * @return AssetUploadList
     */
    public function getAssetUploadList(): AssetUploadList
    {
        return $this->assetUploadList;
    }
}
