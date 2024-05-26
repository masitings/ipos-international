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

class ObjectbricksAndFieldcollectionExtractor extends AbstractDataObjectFieldExtractor
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
        return
            $fieldDefinition instanceof Data\Objectbricks ||
            $fieldDefinition instanceof Data\Fieldcollections;
    }

    /**
     * @param DataObjectConfig $config
     * @param Data\Objectbricks|Data\Fieldcollections $fieldDefinition
     * @param array $context
     *
     * @return DownloadType|DownloadType[]|null
     */
    public function extract(DataObjectConfig $config, Data $fieldDefinition, array $context = [])
    {
        $types = $fieldDefinition->getAllowedTypes();

        if (empty($types)) {
            return null;
        }

        $downloadTypes = [];

        foreach ($types as $type) {
            $definition = null;

            if ($fieldDefinition instanceof Data\Objectbricks) {
                $definition = \Pimcore\Model\DataObject\Objectbrick\Definition::getByKey($type);
            } elseif ($fieldDefinition instanceof Data\Fieldcollections) {
                $definition = \Pimcore\Model\DataObject\Fieldcollection\Definition::getByKey($type);
            }

            if ($definition && !empty($definition->getLayoutDefinitions())) {
                $this->provideByDefinitions($config, $type, $fieldDefinition, $definition->getLayoutDefinitions(), $downloadTypes, $context);
            }
        }

        return $downloadTypes;
    }

    /**
     * @param DataObjectConfig $config
     * @param string $type
     * @param $parentDefinition
     * @param $fieldDefinition
     * @param array $downloadTypes
     * @param array $context
     */
    public function provideByDefinitions(DataObjectConfig $config, string $type, $parentDefinition, $fieldDefinition, array &$downloadTypes, array $context = [])
    {
        $this->dataObjectFieldsProvider->provideByDefinitions(
            $config,
            $fieldDefinition,
            $downloadTypes, array_replace($context, [
                Context::CONTAINER_TYPE => $parentDefinition->getFieldtype(),
                Context::CONTAINER => $parentDefinition->getName(),
                Context::ATTRIBUTE => $type
            ])
        );
    }
}
