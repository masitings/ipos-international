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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Security;

use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Model\User;
use Pimcore\Tool;
use Symfony\Component\Intl\Locale;

class LanguagesService
{
    const VISIBLE_LANGUAGES = 'visibleLanguages';
    const EDITABLE_LANGUAGES = 'editableLanguages';

    const DEFAULT_LANG = 'default';

    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * LanguagesService constructor.
     *
     * @param SecurityService $securityService
     * @param DataPoolConfigService $dataPoolConfigService
     */
    public function __construct(SecurityService $securityService, DataPoolConfigService $dataPoolConfigService)
    {
        $this->securityService = $securityService;
        $this->dataPoolConfigService = $dataPoolConfigService;
    }

    public function getVisibleLanguages(): array
    {
        return $this->getLanguages(self::VISIBLE_LANGUAGES);
    }

    public function getEditableLanguages(): array
    {
        return $this->getLanguages(self::EDITABLE_LANGUAGES);
    }

    public function getLanguageConfig(): array
    {
        $config = [];

        foreach (Tool::getValidLanguages() as $language) {
            $config[$language] = [
                'name' => Locale::getDisplayName($language),
                'icon' => str_replace(PIMCORE_WEB_ROOT, '', Tool::getLanguageFlagFile($language))
            ];
        }

        return $config;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getSelectStore(): array
    {
        $options = [];
        $locales = Tool::getSupportedLocales();
        foreach (Tool::getValidLanguages() as $language) {
            $options[] = [$language, $locales[$language] . ' (' . $language . ')'];
        }

        return $options;
    }

    public function sortLanguages(array $languages)
    {
        uasort($languages, function ($a, $b) {
            return $this->compareLanguages($a, $b);
        });

        return array_values($languages);
    }

    public function compareLanguages($langA, $langB)
    {
        if ($langA === self::DEFAULT_LANG) {
            return -1;
        }

        if ($langB === self::DEFAULT_LANG) {
            return 1;
        }

        $pimcoreUser = User::getById($this->securityService->getPimcoreUserId());
        $langOrder = $pimcoreUser ? $pimcoreUser->getContentLanguages() : Tool::getValidLanguages();
        $indexA = array_search($langA, $langOrder);
        $indexB = array_search($langB, $langOrder);

        if ($indexA !== false && $indexB === false) {
            return -1;
        }

        if ($indexA === false && $indexB !== false) {
            return 1;
        }

        return strcmp($indexA, $indexB);
    }

    protected function getLanguages(string $languageType): array
    {
        /**
         * @var PortalUserInterface $user
         */
        $user = $this->securityService->getPortalUser();
        if ($user->getAdmin()) {
            $languages = Tool::getValidLanguages();
        } else {
            $languages = (array)$user->get($languageType);

            foreach ((array)$user->getGroups() as $userGroup) {
                $languages = array_merge($languages, (array)$userGroup->get($languageType));
            }

            $languages = sizeof($languages) ? $languages : Tool::getValidLanguages();
        }

        if ($dataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig()) {
            $getter = 'get' . ucfirst($languageType);
            $dataPoolLanguages = sizeof($dataPoolConfig->$getter()) ? $dataPoolConfig->$getter() : Tool::getValidLanguages();
            $languages = array_intersect($languages, $dataPoolLanguages);
        }

        return $this->sortLanguages(array_unique($languages));
    }
}
