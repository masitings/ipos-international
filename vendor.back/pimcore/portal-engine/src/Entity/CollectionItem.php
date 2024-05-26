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

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="portal_engine_collection_item")
 */
class CollectionItem extends AbstractElementItem
{
    const TABLE = 'portal_engine_collection_item';

    /**
     * @var Collection|null
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Pimcore\Bundle\PortalEngineBundle\Entity\Collection")
     * @ORM\JoinColumn(name="collectionId", referencedColumnName="id")
     */
    private $collection;

    /**
     * @return Collection|null
     */
    public function getCollection(): ?Collection
    {
        return $this->collection;
    }

    /**
     * @param Collection|null $collection
     *
     * @return CollectionItem
     */
    public function setCollection(?Collection $collection): self
    {
        $this->collection = $collection;

        return $this;
    }
}
