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
use Symfony\Component\HttpFoundation\Response;

class DummyDirectEditConnector implements DirectEditConnectorInterface
{
    protected function notSupported()
    {
        return new Response('Direct Edit Bundle might not be installed. Pleas check your setup', Response::HTTP_NOT_FOUND);
    }

    public function generateLink(int $assetId)
    {
        // just a dummy, nothing to do here, this dummy is only used, if the direct edit bundle is not installed
        return $this->notSupported();
    }

    public function cancelEdit(int $assetId)
    {
        // just a dummy, nothing to do here, this dummy is only used, if the direct edit bundle is not installed
        return $this->notSupported();
    }

    public function confirmEdit(int $assetId, Request $request)
    {
        // just a dummy, nothing to do here, this dummy is only used, if the direct edit bundle is not installed
        return $this->notSupported();
    }

    public function confirmOverwriteAfterLocalEdit(int $assetId)
    {
        // just a dummy, nothing to do here, this dummy is only used, if the direct edit bundle is not installed
        return $this->notSupported();
    }

    public function confirmVersionSaveAfterLocalEdit(int $assetId)
    {
        // just a dummy, nothing to do here, this dummy is only used, if the direct edit bundle is not installed
        return $this->notSupported();
    }

    public function eventServerHasGone(int $assetId)
    {
        // just a dummy, nothing to do here, this dummy is only used, if the direct edit bundle is not installed
        return $this->notSupported();
    }
}
