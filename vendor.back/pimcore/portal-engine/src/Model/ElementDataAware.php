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

namespace Pimcore\Bundle\PortalEngineBundle\Model;

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\ContentConfig;
use Pimcore\Model\Document\Editable;
use Pimcore\Model\Document\Editable\Block;
use Pimcore\Model\Document\Editable\Image;
use Pimcore\Model\Document\Editable\Relation;
use Pimcore\Model\Document\Page;

trait ElementDataAware
{
    /**
     * @var Page
     */
    protected $document;

    public function getDocument(): Page
    {
        return $this->document;
    }

    public function getElementData($fieldName)
    {
        $element = $this->document->getEditable($fieldName);

        return $this->interpretElementData($element);
    }

    public function getBlockItemElementData(Block\Item $blockItem, $fieldName)
    {
        $element = $blockItem->getEditable($fieldName);

        return $this->interpretElementData($element);
    }

    protected function interpretElementData(Editable $element = null)
    {
        if ($element) {
            if ($element instanceof Relation) {
                return $element->getElement();
            } elseif ($element instanceof Image) {
                return $element->getImage();
            }

            return $element->getData();
        }

        return null;
    }

    /**
     * @return \Pimcore\Model\Asset\Image|string|null
     */
    public function getIcon()
    {
        $iconData = null;

        if ($image = $this->getElementData(ContentConfig::NAVIGATION_ICON_ASSET)) {
            return $image;
        } elseif ($icon = $this->getElementData(ContentConfig::NAVIGATION_ICON)) {
            return $icon;
        }

        return null;
    }
}
