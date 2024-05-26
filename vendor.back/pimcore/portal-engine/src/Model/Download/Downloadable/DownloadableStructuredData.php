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

class DownloadableStructuredData extends AbstractDownloadable
{
    private $element;

    private $downloadFormat;

    /**
     * @param mixed $element
     *
     * @return $this
     */
    public function setElement($element)
    {
        $this->element = $element;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @return string
     */
    public function getDownloadFormat(): string
    {
        return $this->downloadFormat;
    }

    /**
     * @param string $downloadFormat
     *
     * @return $this
     */
    public function setDownloadFormat(string $downloadFormat)
    {
        $this->downloadFormat = $downloadFormat;

        return $this;
    }
}
