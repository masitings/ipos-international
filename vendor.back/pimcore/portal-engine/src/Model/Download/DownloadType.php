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

class DownloadType extends AbstractDownloadModel
{
    public function __construct()
    {
        parent::__construct([
            'label' => null,
            'formats' => []
        ]);
    }

    /**
     * @param array $formats
     *
     * @return $this
     */
    public function setFormats(array $formats)
    {
        $this->set('formats', $formats);

        return $this;
    }

    /**
     * @return array
     */
    public function getFormats(): array
    {
        return $this->get('formats', []);
    }

    /**
     * @param string|null $id
     * @param string $label
     * @param array|null $setup
     *
     * @return $this
     */
    public function prependFormat(?string $id, string $label, ?array $setup = null)
    {
        $format = ['id' => $id, 'label' => $label, 'setup' => $setup];

        $this->setFormats(array_merge([$format], $this->getFormats()));

        return $this;
    }

    /**
     * @param string|null $id
     * @param string $label
     * @param array|null $setup
     *
     * @return $this
     */
    public function addFormat(?string $id, string $label, ?array $setup = null)
    {
        $format = ['id' => $id, 'label' => $label, 'setup' => $setup];

        $this->setFormats(array_merge($this->getFormats(), [$format]));

        return $this;
    }
}
