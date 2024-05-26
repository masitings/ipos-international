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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Configuration;

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables;
use Pimcore\Bundle\PortalEngineBundle\Model\ElementDataAware;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Document\PageSnippet;

class PortalConfig
{
    use ElementDataAware;

    /**
     * @param Page $document
     */
    public function __construct(Page $document)
    {
        $this->document = $document;
    }

    public function getPortalId(): int
    {
        return $this->document->getId();
    }

    /**
     * @return Page
     */
    public function getPortalDocument(): Page
    {
        return $this->document;
    }

    /**
     * @return string
     */
    public function getPortalName(): ?string
    {
        return $this->getElementData(Editables\PortalConfig::PORTAL_NAME);
    }

    /**
     * @return PageSnippet
     */
    public function getFooterSnippet(): ?PageSnippet
    {
        $snippet = $this->getElementData(Editables\PortalConfig::FOOTER_SNIPPET);

        return $snippet instanceof PageSnippet ? $snippet : null;
    }

    /**
     * @return PageSnippet
     */
    public function getPublicFooterSnippet(): ?PageSnippet
    {
        return $this->getElementData(Editables\PortalConfig::PUBLIC_FOOTER_SNIPPET) ?: null;
    }

    /**
     * @return Image
     */
    public function getLogo(): ?Image
    {
        $logo = $this->getElementData(Editables\PortalConfig::LOGO);

        return $logo instanceof Image ? $logo : null;
    }

    public function getCustomizedFrontendBuild(): ?string
    {
        return $this->getElementData(Editables\PortalConfig::CUSTOMIZED_FRONTEND_BUILD);
    }

    /**
     * @return string
     */
    public function getEnableLanguageRedirect(): bool
    {
        return (bool) $this->getElementData(Editables\PortalConfig::ENABLE_LANGUAGE_REDIRECT);
    }
}
