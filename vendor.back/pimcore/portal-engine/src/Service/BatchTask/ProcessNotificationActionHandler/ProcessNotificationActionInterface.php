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

namespace Pimcore\Bundle\PortalEngineBundle\Service\BatchTask\ProcessNotificationActionHandler;

use Pimcore\Bundle\PortalEngineBundle\Entity\BatchTask;
use Symfony\Component\HttpFoundation\Response;

interface ProcessNotificationActionInterface
{
    public function supports(BatchTask $batchTask): bool;

    public function handle(BatchTask $batchTask): Response;

    public function terminate(BatchTask $batchTask);
}
