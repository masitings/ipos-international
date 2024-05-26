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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Content;

use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Document\DocumentStack;
use Pimcore\Model\Document;
use Pimcore\Translation\Translator;

/**
 * Class HeadTitleService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Content
 */
class HeadTitleService
{
    /** @var DocumentStack */
    protected $documentStack;
    /** @var Translator */
    protected $translator;
    /** @var PortalConfigService */
    protected $portalConfigService;

    /** @var string|null */
    protected $title = null;

    /**
     * HeadTitleService constructor.
     *
     * @param DocumentStack $documentStack
     * @param Translator $translator
     */
    public function __construct(DocumentStack $documentStack, Translator $translator, PortalConfigService $portalConfigService)
    {
        $this->documentStack = $documentStack;
        $this->translator = $translator;
        $this->portalConfigService = $portalConfigService;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        /** @var string|null $title */
        $title = $this->title;

        if (!$title) {
            /** @var Document|null $currentDocument */
            $currentDocument = $this->documentStack->getCurrentDocument();
            if ($currentDocument instanceof Document\Page) {
                $title = $currentDocument->getTitle() ?: $currentDocument->getProperty('navigation_name');
            }
        }

        if (!$title) {
            $title = $this->translator->trans('portal-engine.content.document-default-title');
        }

        return $this->appendTitleSuffix($title);
    }

    protected function appendTitleSuffix(string $title): string
    {
        return $title . ' | ' . $this->getPortalName();
    }

    protected function getPortalName(): string
    {
        return $this->portalConfigService->getPortalName();
    }

    /**
     * @param string|null $title
     *
     * @return HeadTitleService
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
