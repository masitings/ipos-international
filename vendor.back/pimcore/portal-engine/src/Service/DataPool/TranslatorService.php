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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataPool;

use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\TranslatorDomain;
use Pimcore\Localization\LocaleServiceInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TranslatorService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\DataPool
 */
class TranslatorService
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var LocaleServiceInterface */
    protected $localeService;

    /**
     * TranslatorService constructor.
     *
     * @param TranslatorInterface $translator
     * @param LocaleServiceInterface $localeService
     */
    public function __construct(TranslatorInterface $translator, LocaleServiceInterface $localeService)
    {
        $this->translator = $translator;
        $this->localeService = $localeService;
    }

    /**
     * get translationValue by $domain and $key. if $key is not translated in pimcore shared translations use the $key as return value
     *
     * @param string $key
     * @param string|null $domain
     *
     * @return string
     */
    public function translate(string $key, ?string $domain = null)
    {
        try {
            /** @var string $translationKey */
            $translationKey = trim($this->getDomainPrefix($domain) . $key);

            if (strlen($translationKey) > 190) {
                throw new \Exception('translation keys longer than 190 characters are invalid!');
            }

            /** @var string $translationValue */
            $translationValue = $this->translator->trans($translationKey, [], null, $this->localeService->getLocale());

            if ($translationValue === $translationKey) {
                throw new \Exception('translation value not found');
            }
        } catch (\Exception $e) {
            $translationValue = $key;
        }

        return $translationValue;
    }

    /**
     * @param string|null $domain
     *
     * @return string
     */
    public function getDomainPrefix(?string $domain = null)
    {
        if (!$domain || $domain === TranslatorDomain::TRANSLATION_DOMAIN_PREFIX) {
            return TranslatorDomain::TRANSLATION_DOMAIN_PREFIX . '.';
        }

        return TranslatorDomain::TRANSLATION_DOMAIN_PREFIX . ".{$domain}.";
    }
}
