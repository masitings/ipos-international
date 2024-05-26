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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Messenger\Middleware;

use Pimcore\Bundle\PortalEngineBundle\Traits\LoggerAware;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class CollectGarbageMiddleware implements MiddlewareInterface
{
    use LoggerAware;

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $this->logger->debug('Execute collect garbage middleware');
        \Pimcore::collectGarbage();

        return $stack->next()->handle($envelope, $stack);
    }
}
