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

use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataPool\FieldDefinitionAdapter\FieldDefinitionAdapterInterface;

/**
 * Class ExportableField
 */
class ExportableField
{
    /** @var string|null */
    protected $type;

    /** @var string|null */
    protected $title;

    /** @var string|null */
    protected $name;

    /** @var mixed */
    protected $data;

    /**
     * @var bool
     */
    protected $localized = false;

    /**
     * @var FieldDefinitionAdapterInterface
     */
    protected $fieldDefinitionAdapter;

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return ListableField
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

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
     * @return ExportableField
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
     * @return ExportableField
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return ExportableField
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLocalized(): bool
    {
        return $this->localized;
    }

    /**
     * @param bool $localized
     *
     * @return ExportableField
     */
    public function setLocalized(bool $localized): self
    {
        $this->localized = $localized;

        return $this;
    }

    /**
     * @return FieldDefinitionAdapterInterface
     */
    public function getFieldDefinitionAdapter(): FieldDefinitionAdapterInterface
    {
        return $this->fieldDefinitionAdapter;
    }

    /**
     * @param FieldDefinitionAdapterInterface $fieldDefinitionAdapter
     *
     * @return ExportableField
     */
    public function setFieldDefinitionAdapter(FieldDefinitionAdapterInterface $fieldDefinitionAdapter): self
    {
        $this->fieldDefinitionAdapter = $fieldDefinitionAdapter;

        return $this;
    }
}
