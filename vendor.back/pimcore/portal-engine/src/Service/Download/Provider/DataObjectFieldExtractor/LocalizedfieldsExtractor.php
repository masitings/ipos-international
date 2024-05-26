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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\DataObjectFieldExtractor;

use Pimcore\Bundle\PortalEngineBundle\Enum\Download\Context;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadType;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\DataObjectFieldsProvider;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class LocalizedfieldsExtractor extends AbstractDataObjectFieldExtractor
{
    protected $dataObjectFieldsProvider;

    public function __construct(DownloadService $downloadService, DataObjectFieldsProvider $dataObjectFieldsProvider)
    {
        parent::__construct($downloadService);

        $this->dataObjectFieldsProvider = $dataObjectFieldsProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Data $fieldDefinition): bool
    {
        return $fieldDefinition instanceof Data\Localizedfields;
    }

    /**
     * @param DataObjectConfig $config
     * @param Data\Localizedfields $fieldDefinition
     * @param array $context
     *
     * @return DownloadType|DownloadType[]
     */
    public function extract(DataObjectConfig $config, Data $fieldDefinition, array $context = [])
    {
        $downloadTypes = [];

        if (!empty($fieldDefinition->getFieldDefinitions())) {
            foreach ($fieldDefinition->getFieldDefinitions() as $fd) {
                $this->dataObjectFieldsProvider->provideByDefinitions(
                    $config,
                    $fd,
                    $downloadTypes,
                    array_replace($context, [Context::LOCALIZED => true])
                );
            }
        }

        return $downloadTypes;
    }
}
