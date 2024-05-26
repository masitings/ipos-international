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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field;

use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter\FieldDefinitionAdapterInterface;

/**
 * Class FilterableField
 */
class FilterableField
{
    /** @var string|null */
    protected $path;
    /** @var string|null */
    protected $title;
    /** @var string|null */
    protected $name;
    /** @var FieldDefinitionAdapterInterface|\Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter\FieldDefinitionAdapterInterface */
    protected $fieldDefinitionAdapter;

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     *
     * @return $this
     */
    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return $this
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter\FieldDefinitionAdapterInterface|FieldDefinitionAdapterInterface
     */
    public function getFieldDefinitionAdapter()
    {
        return $this->fieldDefinitionAdapter;
    }

    /**
     * @param \Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter\FieldDefinitionAdapterInterface|FieldDefinitionAdapterInterface $fieldDefinitionAdapter
     *
     * @return $this
     */
    public function setFieldDefinitionAdapter($fieldDefinitionAdapter): self
    {
        $this->fieldDefinitionAdapter = $fieldDefinitionAdapter;

        return $this;
    }
}
