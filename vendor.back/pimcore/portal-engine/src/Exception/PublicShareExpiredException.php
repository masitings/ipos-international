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

namespace Pimcore\Bundle\PortalEngineBundle\Exception;

use Pimcore\Bundle\PortalEngineBundle\Entity\PublicShare;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PublicShareExpiredException
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Exception
 */
class PublicShareExpiredException extends NotFoundHttpException
{
    /**
     * @var PublicShare
     */
    private $publicShare;

    /**
     * @return PublicShare
     */
    public function getPublicShare(): PublicShare
    {
        return $this->publicShare;
    }

    /**
     * @param PublicShare $publicShare
     */
    public function setPublicShare(PublicShare $publicShare): void
    {
        $this->publicShare = $publicShare;
    }
}
