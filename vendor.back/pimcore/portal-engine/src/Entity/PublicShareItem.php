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
 * @ORM\Table(name="portal_engine_public_share_item")
 */
class PublicShareItem extends AbstractElementItem
{
    const TABLE = 'portal_engine_public_share_item';

    /**
     * @var PublicShare|null
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Pimcore\Bundle\PortalEngineBundle\Entity\PublicShare")
     * @ORM\JoinColumn(name="publicShareId", referencedColumnName="id")
     */
    private $publicShare;

    /**
     * @return PublicShare|null
     */
    public function getPublicShare(): ?PublicShare
    {
        return $this->publicShare;
    }

    /**
     * @param PublicShare|null $publicShare
     *
     * @return PublicShareItem
     */
    public function setPublicShare(?PublicShare $publicShare): self
    {
        $this->publicShare = $publicShare;

        return $this;
    }
}
