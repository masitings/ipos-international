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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Download;

class DownloadConfig extends AbstractDownloadModel
{
    public function __construct()
    {
        parent::__construct([
            'format' => null,
            'label' => null,
            'setup' => null
        ]);
    }

    /**
     * @param string|null $label
     *
     * @return $this
     */
    public function setLabel(?string $label)
    {
        $this->set('label', $label);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->get('label', null);
    }

    /**
     * @param string|null $format
     *
     * @return $this
     */
    public function setFormat(?string $format)
    {
        $this->set('format', $format);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->get('format', null);
    }

    /**
     * @param array|null $setup
     *
     * @return $this
     */
    public function setSetup(?array $setup)
    {
        $this->set('setup', $setup);

        return $this;
    }

    /**
     * @return array|null
     */
    public function getSetup(): ?array
    {
        return $this->get('setup');
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return md5(implode('_', [
            $this->getAttribute(),
            $this->getFormat()
        ]));
    }
}
