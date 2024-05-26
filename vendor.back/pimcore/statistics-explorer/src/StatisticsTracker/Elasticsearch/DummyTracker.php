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

namespace Pimcore\Bundle\StatisticsExplorerBundle\StatisticsTracker\Elasticsearch;

use Pimcore\Model\Asset;

class DummyTracker extends AbstractElasticsearchTracker
{
    protected function getIndexName(): string
    {
        return 'tracking_test';
    }

    protected function buildMappingArray(): array
    {
        return [
            'timestamp' => ['type' => 'date'],
            'asset' => [
                'type' => 'object',
                'dynamic' => false,
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'type' => ['type' => 'keyword'],
                    'path' => ['type' => 'keyword']
                ]
            ]
        ];
    }

    public function trackEvent(array $parameters): void
    {
        $datetime = new \DateTime();
        $values = [
            'timestamp' => $datetime->format('c'),
            'asset' => $this->trackAsset($parameters['asset'])
        ];
        $this->doTrackEvent($values);
    }

    protected function trackAsset(Asset $asset)
    {
        return [
            'id' => $asset->getId(),
            'type' => $asset->getMimetype(),
            'path' => $asset->getFullPath()
        ];
    }
}
