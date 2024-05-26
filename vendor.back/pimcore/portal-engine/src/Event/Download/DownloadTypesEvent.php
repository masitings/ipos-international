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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Download;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadType;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires after the download types got determined.
 * Can be used add or remove download formats (for example depending on an additional user permission logic).
 */
class DownloadTypesEvent extends Event
{
    private $dataPoolConfig;
    private $downloadTypes;

    public function __construct(DataPoolConfigInterface $dataPoolConfig, array $downloadTypes)
    {
        $this->dataPoolConfig = $dataPoolConfig;
        $this->downloadTypes = $downloadTypes;
    }

    /**
     * @return DownloadType[]
     */
    public function getDownloadTypes()
    {
        return $this->downloadTypes;
    }

    /**
     * @param DownloadType[] $downloadTypes
     */
    public function setDownloadTypes(array $downloadTypes)
    {
        $this->downloadTypes = $downloadTypes;
    }
}
