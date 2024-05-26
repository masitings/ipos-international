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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable;

class GenerationResult
{
    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * GenerationResult constructor.
     *
     * @param string $filePath
     * @param string $fileName
     */
    public function __construct(string $fileName, string $filePath)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}
