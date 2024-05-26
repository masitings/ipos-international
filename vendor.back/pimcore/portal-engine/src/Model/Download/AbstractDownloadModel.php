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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Download;

use Pimcore\Bundle\PortalEngineBundle\Model\BasicJsonModel;

abstract class AbstractDownloadModel extends BasicJsonModel
{
    public function __construct(array $params)
    {
        parent::__construct(array_replace([
            'type' => null,
            'attribute' => null,
            'localized' => false
        ], $params));
    }

    /**
     * @param string|null $type
     *
     * @return $this
     */
    public function setType(?string $type)
    {
        $this->set('type', $type);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->get('type');
    }

    /**
     * @param string|null $attribute
     *
     * @return $this
     */
    public function setAttribute(?string $attribute)
    {
        $this->set('attribute', $attribute);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAttribute(): ?string
    {
        return $this->get('attribute');
    }

    /**
     * @param bool $localized
     *
     * @return $this
     */
    public function setLocalized(bool $localized)
    {
        $this->set('localized', $localized);

        return $this;
    }

    /**
     * @return bool
     */
    public function getLocalized(): bool
    {
        return $this->get('localized', false);
    }

    /**
     * @param string|null $label
     *
     * @return $this
     */
    public function setLabel(?string $label)
    {
        $this->set('label', $label);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->get('label');
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return $this->all();
    }
}
