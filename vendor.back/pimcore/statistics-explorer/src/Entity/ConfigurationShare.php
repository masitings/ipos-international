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

namespace Pimcore\Bundle\StatisticsExplorerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="bundle_statistics_explorer_configuration_share")
 */
class ConfigurationShare
{
    const TABLE = 'bundle_statistics_explorer_configuration_share';

    /**
     * @var string|null
     *
     * @ORM\Id()
     * @ORM\Column(type="string", length=20)
     */
    private $sharedWithType;

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $sharedWithId;

    /**
     * @var Configuration|null
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Pimcore\Bundle\StatisticsExplorerBundle\Entity\Configuration")
     * @ORM\JoinColumn(name="configurationId", referencedColumnName="id")
     */
    private $configuration;

    /**
     * @return string|null
     */
    public function getSharedWithType(): ?string
    {
        return $this->sharedWithType;
    }

    /**
     * @param string|null $sharedWithType
     */
    public function setSharedWithType(?string $sharedWithType): void
    {
        $this->sharedWithType = $sharedWithType;
    }

    /**
     * @return int|null
     */
    public function getSharedWithId(): ?int
    {
        return $this->sharedWithId;
    }

    /**
     * @param int|null $sharedWithId
     */
    public function setSharedWithId(?int $sharedWithId): void
    {
        $this->sharedWithId = $sharedWithId;
    }

    /**
     * @return Configuration|null
     */
    public function getConfiguration(): ?Configuration
    {
        return $this->configuration;
    }

    /**
     * @param Configuration|null $configuration
     */
    public function setConfiguration(?Configuration $configuration): void
    {
        $this->configuration = $configuration;
    }
}
