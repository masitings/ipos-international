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

use Pimcore\Bundle\PortalEngineBundle\Event\Asset\ExtractNameEvent;
use Pimcore\Model\Asset;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NameExtractorService
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * NameExtractorService constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function extractName(Asset $asset, string $locale = null): string
    {
        $event = new ExtractNameEvent($asset);

        $this->eventDispatcher->dispatch($event);

        if (!is_null($event->getName())) {
            return $event->getName();
        }

        return $asset->getFilename();
    }
}
