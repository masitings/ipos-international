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

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\DataObjectFieldExtractor\ObjectbricksAndFieldcollectionExtractor;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Traits\FieldcollectionObjectbrickDefinitionTrait;

abstract class AbstractNestedLayoutService
{
    protected $objectbricksAndFieldcollectionExtractor;
    protected $customLayoutService;

    public function __construct(ObjectbricksAndFieldcollectionExtractor $objectbricksAndFieldcollectionExtractor, CustomLayoutService $customLayoutService)
    {
        $this->objectbricksAndFieldcollectionExtractor = $objectbricksAndFieldcollectionExtractor;
        $this->customLayoutService = $customLayoutService;
    }

    /**
     * @param ClassDefinition $classDefinition
     *
     * @return array
     */
    public function getLayoutDefinitionsForClassDefinition(DataPoolConfigInterface $config, ClassDefinition $classDefinition, $object)
    {
        $fieldDefinitions = $classDefinition->getFieldDefinitions();

        if (empty($fieldDefinitions)) {
            return [];
        }

        $layoutDefinitions = [];

        foreach ($fieldDefinitions as $fieldDefinition) {
            if (!$this->supports($fieldDefinition) || (method_exists($fieldDefinition, 'getAllowedTypes') && empty($fieldDefinition->getAllowedTypes()))) {
                continue;
            }

            foreach ($fieldDefinition->getAllowedTypes() as $type) {
                try {
                    $layoutDefinitions[$type] = $this->getLayoutDefinitionsByType($type, $config, $fieldDefinition, $object);
                } catch (\Exception $e) {
                }
            }
        }

        return $layoutDefinitions;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getLayoutDefinitionsByType(string $type, DataPoolConfigInterface $config, $fieldDefinition, $object)
    {
        $definition = $this->getDefinitionByType($type);

        return $this->customLayoutService->enrichLayoutDefinition($definition->getLayoutDefinitions(), $config, $object, function ($layout, $config, $object, &$item) use ($type, $fieldDefinition) {
            $downloadTypes = [];
            $this->objectbricksAndFieldcollectionExtractor->provideByDefinitions($config, $type, $fieldDefinition, $layout, $downloadTypes);
            $item['portalDownloadTypes'] = $downloadTypes;
        });
    }

    /**
     * @param ClassDefinition\Data $fieldDefinition
     *
     * @return bool
     */
    abstract protected function supports(ClassDefinition\Data $fieldDefinition): bool;

    /**
     * @param string $type
     *
     * @return FieldcollectionObjectbrickDefinitionTrait
     */
    abstract protected function getDefinitionByType(string $type);
}
