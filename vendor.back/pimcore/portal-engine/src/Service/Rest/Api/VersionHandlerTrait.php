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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api;

use Carbon\Carbon;
use Pimcore\Bundle\PortalEngineBundle\Event\Element\VersionHistoryEvent;
use Pimcore\Localization\IntlFormatter;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Version;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

trait VersionHandlerTrait
{
    /**
     * @param array $versions
     *
     * @return ElementInterface[]
     */
    public function extractVersionElements(array $versions)
    {
        $elements = array_map(function (Version $version) {
            return $this->extractVersionElement($version);
        }, $versions);

        return $elements;
    }

    /**
     * @param Version $version
     *
     * @return ElementInterface
     */
    public function extractVersionElement(Version $version)
    {
        AbstractObject::setDoNotRestoreKeyAndPath(true);
        $element = $version->loadData();
        AbstractObject::setDoNotRestoreKeyAndPath(false);

        return $element;
    }

    /**
     * @param ElementInterface $element
     * @param IntlFormatter $formatter
     *
     * @return array
     */
    public function extractVersionsData(ElementInterface $element, IntlFormatter $formatter, EventDispatcherInterface $eventDispatcher)
    {
        $versions = $element->getVersions();

        $event = new VersionHistoryEvent($versions ?: []);
        $eventDispatcher->dispatch($event);

        $versions = array_filter($event->getVersions());

        uasort($versions, function (Version $a, Version $b) {
            if ($a->getDate() === $b->getDate()) {
                return 0;
            }

            return ($a->getDate()) > $b->getDate() ? -1 : 1;
        });

        return array_values(array_map(function (Version $version, $index) use ($element, $formatter) {
            return [
                'id' => $version->getId(),
                'published' => $element->getVersionCount() === $version->getVersionCount(),
                'userId' => $version->getUserId(),
                'note' => $version->getNote(),
                'date' => $formatter->formatDateTime(Carbon::createFromTimestamp($version->getDate()), IntlFormatter::DATETIME_SHORT)
            ];
        }, $versions, array_keys($versions)));
    }
}
