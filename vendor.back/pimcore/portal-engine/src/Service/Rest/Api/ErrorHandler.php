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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api;

/**
 * Class ErrorHandler
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api
 */
class ErrorHandler
{
    /** @var string|null */
    protected $errorMessage;

    /**
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->errorMessage);
    }

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @param string|null $errorMessage
     *
     * @return ErrorHandler
     */
    public function setErrorMessage(?string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }
}
