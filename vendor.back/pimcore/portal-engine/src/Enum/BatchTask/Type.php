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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask;

class Type
{
    const BATCH_TASK_NOTIFICATION_TYPE = 'batch-task';
    const DOWNLOAD_GENERATION = 'download-generation';
    const DELETE_ASSET = 'delete-asset';
    const RELOCATE_ASSET = 'relocate-asset';
    const UPDATE_ASSET_METADATA = 'update-asset-metadata';
}
