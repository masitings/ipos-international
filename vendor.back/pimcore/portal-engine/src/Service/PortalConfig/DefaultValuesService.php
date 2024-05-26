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

namespace Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig;

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\PortalConfig;
use Pimcore\Model\Document\Editable\Checkbox;
use Pimcore\Model\Document\Editable\Input;
use Pimcore\Model\Document\PageSnippet;

class DefaultValuesService
{
    public function setPortalPageDefaultConfig(PageSnippet $document)
    {
        $this->setInputDefaultValue($document, PortalConfig::COLOR_PRIMARY, '#2B0080');
        $this->setInputDefaultValue($document, PortalConfig::COLOR_SECONDARY, '#282828');
        $this->setInputDefaultValue($document, PortalConfig::COLOR_DARK, '#282828');
        $this->setInputDefaultValue($document, PortalConfig::COLOR_LIGHT, '#ffffff');
        $this->setInputDefaultValue($document, PortalConfig::COLOR_HEADER, '#2B0080');
        $this->setInputDefaultValue($document, PortalConfig::BTN_PRIMARY_TEXT_COLOR, '#FFFFFF');
        $this->setInputDefaultValue($document, PortalConfig::BTN_SECONDARY_TEXT_COLOR, '#FFFFFF');
        $this->setInputDefaultValue($document, PortalConfig::BTN_DARK_TEXT_COLOR, '#FFFFFF');
        $this->setInputDefaultValue($document, PortalConfig::BTN_LIGHT_TEXT_COLOR, '#181818');
        $this->setInputDefaultValue($document, PortalConfig::HEADER_TEXT_COLOR, '#FFFFFF');
    }

    public function setPortalPreCreateDefaultConfig(PageSnippet $document)
    {
        $this->setPortalPageDefaultConfig($document);

        $this->setCheckboxDefaultValue($document, PortalConfig::HEADER_GRADIENTS, true);
        $this->setCheckboxDefaultValue($document, PortalConfig::NAV_ICON_GRADIENTS, true);
        $this->setCheckboxDefaultValue($document, PortalConfig::MODAL_GRADIENTS, true);
    }

    protected function setInputDefaultValue(PageSnippet $document, string $elementName, string $defaultValue)
    {
        $tag = $document->getEditable($elementName);
        if (!empty($tag) && $tag->isEmpty()) {
            $tag->setDataFromEditmode($defaultValue);
        } elseif (empty($tag)) {
            $tag = new Input();
            $tag->setName($elementName);
            $tag->setDataFromEditmode($defaultValue);
            $document->setEditable($tag);
        }
    }

    protected function setCheckboxDefaultValue(PageSnippet $document, string $elementName, string $defaultValue)
    {
        $tag = $document->getEditable($elementName);
        if (!empty($tag) && $tag->isEmpty()) {
            $tag->setDataFromEditmode($defaultValue);
        } elseif (empty($tag)) {
            $tag = new Checkbox();
            $tag->setName($elementName);
            $tag->setDataFromEditmode($defaultValue);
            $document->setEditable($tag);
        }
    }
}
