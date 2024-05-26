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

namespace Pimcore\Bundle\PortalEngineBundle\Model\View;

/**
 * Class Notification
 */
class Notification
{
    const HTML_CLASS_SUCCESS = 'success';
    const HTML_CLASS_DANGER = 'danger';

    /** @var string|null */
    protected $message;
    /** @var string|null */
    protected $htmlClass;

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     *
     * @return Notification
     */
    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHtmlClass(): ?string
    {
        return $this->htmlClass;
    }

    /**
     * @param string|null $htmlClass
     *
     * @return Notification
     */
    public function setHtmlClass(?string $htmlClass): self
    {
        $this->htmlClass = $htmlClass;

        return $this;
    }
}
