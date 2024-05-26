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

namespace Pimcore\Bundle\PortalEngineBundle\Event\DataObject;

use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires before the elasticsearch mapping will be sent to the elasticsearch index.
 * Can be used to add mappings for customized additional fields.
 * You will find a description and example on how it works in the portal engine docs.
 */
class ExtractMappingEvent extends Event
{
    /** @var ClassDefinition */
    protected $classDefinition;
    /** @var array */
    protected $customFieldsMapping;

    /**
     * ExtractMappingEvent constructor.
     *
     * @param ClassDefinition $classDefinition
     * @param array $customFieldsMapping
     */
    public function __construct(ClassDefinition $classDefinition, array $customFieldsMapping)
    {
        $this->classDefinition = $classDefinition;
        $this->customFieldsMapping = $customFieldsMapping;
    }

    /**
     * @return ClassDefinition
     */
    public function getClassDefinition(): ClassDefinition
    {
        return $this->classDefinition;
    }

    /**
     * @return array
     */
    public function getCustomFieldsMapping(): array
    {
        return $this->customFieldsMapping;
    }

    /**
     * @param array $customFieldsMapping
     *
     * @return ExtractMappingEvent
     */
    public function setCustomFieldsMapping(array $customFieldsMapping): self
    {
        $this->customFieldsMapping = $customFieldsMapping;

        return $this;
    }
}
