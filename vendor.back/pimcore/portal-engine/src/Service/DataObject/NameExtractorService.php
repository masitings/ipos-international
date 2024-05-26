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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataObject;

use Pimcore\Bundle\PortalEngineBundle\Event\DataObject\ExtractNameEvent;
use Pimcore\Model\DataObject\AbstractObject;
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

    /**
     * @param AbstractObject $object
     *
     * @return string
     */
    public function extractName(AbstractObject $object, string $locale = null): string
    {
        $event = new ExtractNameEvent($object, $locale);

        $this->eventDispatcher->dispatch($event);

        if (!is_null($event->getName())) {
            return $event->getName();
        }

        /** @var string|null $name */
        $name = null;
        if (method_exists($object, 'getName')) {
            if ($this->isLocalizedMethod($object, 'getName')) {
                $name = $object->getName($locale);
            } else {
                $name = $object->getName();
            }
        }

        if (is_null($name)) {
            $name = $object->getKey();
        }

        return $name;
    }

    protected function isLocalizedMethod($object, $method): bool
    {
        if (method_exists($object, 'getName')) {
            $method = new \ReflectionMethod($object, 'getName');
            foreach ($method->getParameters() as $parameter) {
                if ($parameter->getName() === 'language') {
                    return true;
                }
            }
        }

        return false;
    }
}
