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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Element;

use Pimcore\Model\Version;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires after the versions of an element got determined for detail pages.
 * Can be used to filter out certain versions which should not be displayed to the user.
 */
class VersionHistoryEvent extends Event
{
    /**
     * @var Version[]
     */
    protected $versions;

    public function __construct(array $versions)
    {
        $this->versions = $versions;
    }

    /**
     * @return Version[]
     */
    public function getVersions(): array
    {
        return $this->versions;
    }

    /**
     * @param Version[] $versions
     *
     * @return $this
     */
    public function setVersions(array $versions): self
    {
        $this->versions = $versions;

        return $this;
    }
}
