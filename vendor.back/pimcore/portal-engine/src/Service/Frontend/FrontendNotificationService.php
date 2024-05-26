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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Frontend;

use Pimcore\Bundle\PortalEngineBundle\Model\View\Notification;

/**
 * Class FrontendNotificationService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Frontend
 */
class FrontendNotificationService
{
    /** @var Notification|null */
    protected $notification = null;

    /**
     * @param string $message
     * @param null $htmlClass
     *
     * @return $this
     */
    public function addNotification(string $message, $htmlClass = null)
    {
        $this->notification = (new Notification())
            ->setMessage($message)
            ->setHtmlClass($htmlClass);

        return $this;
    }

    /**
     * @return Notification|null
     */
    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    /**
     * @param Notification|null $notification
     *
     * @return FrontendNotificationService
     */
    public function setNotification(?Notification $notification): self
    {
        $this->notification = $notification;

        return $this;
    }
}
