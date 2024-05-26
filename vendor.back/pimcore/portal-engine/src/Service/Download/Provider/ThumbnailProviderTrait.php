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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadType;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Generator\OriginalAssetGenerator;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Generator\ThumbnailGenerator;

trait ThumbnailProviderTrait
{
    protected $translatorService;

    protected function addThumbnailsToDownloadType(DataPoolConfigInterface $dataPoolConfig, DownloadType $downloadType)
    {
        if (!empty($dataPoolConfig->getAvailableDownloadThumbnails())) {
            foreach ($dataPoolConfig->getAvailableDownloadThumbnails() as $thumbnail) {
                $label = $this->translatorService->translate($thumbnail, 'thumbnail');

                if ($thumbnail === OriginalAssetGenerator::ORIGINAL_FORMAT) {
                    $downloadType->prependFormat($thumbnail, $label);
                } elseif ($thumbnail === ThumbnailGenerator::CUSTOM_FORMAT) {
                    $downloadType->addFormat($thumbnail, $label, [
                        'width' => [
                            'type' => 'number',
                            'min' => 1
                        ],
                        'height' => [
                            'type' => 'number',
                            'min' => 1
                        ],
                        'format' => [
                            'type' => 'select',
                            'options' => [
                                [
                                    'label' => $this->translatorService->translate('jpeg', 'thumbnail'),
                                    'value' => 'jpeg'
                                ],
                                [
                                    'label' => $this->translatorService->translate('png', 'thumbnail'),
                                    'value' => 'png'
                                ]
                            ],
                            'defaultValue' => 'jpeg'
                        ],
                        'quality' => [
                            'type' => 'number',
                            'defaultValue' => 100,
                            'max' => 100,
                            'min' => 0
                        ]
                    ]);
                } else {
                    $downloadType->addFormat($thumbnail, $label);
                }
            }
        } else {
            // force original, if nothing is selected
            $downloadType->addFormat(
                OriginalAssetGenerator::ORIGINAL_FORMAT,
                $this->translatorService->translate(OriginalAssetGenerator::ORIGINAL_FORMAT, 'thumbnail')
            );
        }
    }
}
