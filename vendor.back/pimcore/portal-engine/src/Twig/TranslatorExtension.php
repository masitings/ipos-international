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

use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\TranslatorService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TranslatorExtension
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Twig
 */
class TranslatorExtension extends AbstractExtension
{
    /** @var TranslatorService */
    protected $translatorService;

    /**
     * TranslatorExtension constructor.
     *
     * @param TranslatorService $translatorService
     */
    public function __construct(TranslatorService $translatorService)
    {
        $this->translatorService = $translatorService;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('portalEngine_translate', [$this, 'translate']),
        ];
    }

    /**
     * @param string $key
     * @param string|null $domain
     *
     * @return string
     */
    public function translate(string $key, ?string $domain = null)
    {
        return $this->translatorService->translate($key, $domain);
    }
}
