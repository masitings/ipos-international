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

namespace Pimcore\Bundle\PortalEngineBundle\Exception\Asset;

use Throwable;

/**
 * Class FileUploadException
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Exception\Asset
 */
class FileUploadException extends \Exception
{
    /** @var string|null */
    protected $filename;

    /**
     * FileUploadException constructor.
     *
     * @param string $message
     * @param null $filename
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $filename = null, $code = 0, Throwable $previous = null)
    {
        $this->filename = $filename;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }
}
