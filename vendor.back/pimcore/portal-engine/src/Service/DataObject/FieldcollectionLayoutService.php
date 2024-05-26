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

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection\Definition;

class FieldcollectionLayoutService extends AbstractNestedLayoutService
{
    /**
     * {@inheritDoc}
     */
    protected function supports(ClassDefinition\Data $fieldDefinition): bool
    {
        return $fieldDefinition instanceof ClassDefinition\Data\Fieldcollections;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefinitionByType(string $type)
    {
        return Definition::getByKey($type);
    }
}
