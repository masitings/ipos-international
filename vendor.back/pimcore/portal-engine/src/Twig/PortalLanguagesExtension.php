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

namespace Pimcore\Bundle\PortalEngineBundle\Twig;

use Pimcore\Bundle\PortalEngineBundle\Service\Document\LanguageVariantService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PortalLanguagesExtension extends AbstractExtension
{
    /**
     * @var LanguageVariantService
     */
    protected $languageVariantService;

    /**
     * @param LanguageVariantService $languageVariantService
     */
    public function __construct(LanguageVariantService $languageVariantService)
    {
        $this->languageVariantService = $languageVariantService;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('portalEngine_hasLanguageSelection', [$this, 'hasLanguageSelection']),
            new TwigFunction('portalEngine_languageSelectionLanguages', [$this, 'getLanguageSelectionLanguages']),
            new TwigFunction('portalEngine_currentLanguageSelection', [$this, 'getCurrentLanguageSelection']),
        ];
    }

    public function hasLanguageSelection(): bool
    {
        return sizeof($this->getLanguageSelectionLanguages()) > 0;
    }

    public function getLanguageSelectionLanguages(): array
    {
        return $this->languageVariantService->getPortalLanguageVariants();
    }

    public function getCurrentLanguageSelection(): string
    {
        return $this->languageVariantService->getCurrentLanguageSelectionDocumentPath();
    }
}
