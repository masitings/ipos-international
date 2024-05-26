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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Asset;

use Symfony\Component\HttpFoundation\Request;

interface DirectEditConnectorInterface
{
    public function generateLink(int $assetId);

    public function cancelEdit(int $assetId);

    public function confirmEdit(int $assetId, Request $request);

    public function confirmOverwriteAfterLocalEdit(int $assetId);

    public function confirmVersionSaveAfterLocalEdit(int $assetId);

    public function eventServerHasGone(int $assetId);
}
