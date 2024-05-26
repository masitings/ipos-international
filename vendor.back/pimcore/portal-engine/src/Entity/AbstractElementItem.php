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

namespace Pimcore\Bundle\PortalEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;

abstract class AbstractElementItem
{
    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    protected $elementId;

    /**
     * @var string|null
     *
     * @ORM\Id()
     * @ORM\Column(type="string", length=20)
     */
    protected $elementType;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=20)
     */
    protected $elementSubType;

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    protected $dataPoolId;

    /**
     * @return int|null
     */
    public function getElementId(): ?int
    {
        return $this->elementId;
    }

    /**
     * @param int|null $elementId
     *
     * @return $this
     */
    public function setElementId(?int $elementId)
    {
        $this->elementId = $elementId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getElementType(): ?string
    {
        return $this->elementType;
    }

    /**
     * @param string|null $elementType
     *
     * @return $this
     */
    public function setElementType(?string $elementType)
    {
        $this->elementType = $elementType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getElementSubType(): ?string
    {
        return $this->elementSubType;
    }

    /**
     * @param string|null $elementSubType
     *
     * @return $this
     */
    public function setElementSubType(?string $elementSubType)
    {
        $this->elementSubType = $elementSubType;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDataPoolId(): ?int
    {
        return $this->dataPoolId;
    }

    /**
     * @param int|null $dataPoolId
     *
     * @return $this
     */
    public function setDataPoolId(?int $dataPoolId)
    {
        $this->dataPoolId = $dataPoolId;

        return $this;
    }

    /**
     * @return ElementInterface|null
     */
    public function getElement(): ?ElementInterface
    {
        return Service::getElementById($this->getElementType(), $this->getElementId());
    }
}
