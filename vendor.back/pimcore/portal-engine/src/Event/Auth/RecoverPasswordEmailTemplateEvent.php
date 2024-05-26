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

namespace Pimcore\Bundle\PortalEngineBundle\Event\Auth;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fires before a password recovery email will be sent.
 * Can be used to customize the HTML body of the mail content.
 * Hint: for most use cases it should be enough to just overwrite the twig template.
 */
class RecoverPasswordEmailTemplateEvent extends Event
{
    /** @var string */
    protected $htmlBody;
    /** @var string|null */
    protected $userName;
    /** @var string */
    protected $passwordRecoverUrl;

    /**
     * RecoverPasswordEmailTemplateEvent constructor.
     *
     * @param string $htmlBody
     * @param string|null $userName
     * @param string $passwordRecoverUrl
     */
    public function __construct(string $htmlBody, ?string $userName, string $passwordRecoverUrl)
    {
        $this->htmlBody = $htmlBody;
        $this->userName = $userName;
        $this->passwordRecoverUrl = $passwordRecoverUrl;
    }

    /**
     * @return string
     */
    public function getHtmlBody(): string
    {
        return $this->htmlBody;
    }

    /**
     * @param string $htmlBody
     *
     * @return RecoverPasswordEmailTemplateEvent
     */
    public function setHtmlBody(string $htmlBody): self
    {
        $this->htmlBody = $htmlBody;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getPasswordRecoverUrl(): string
    {
        return $this->passwordRecoverUrl;
    }
}
