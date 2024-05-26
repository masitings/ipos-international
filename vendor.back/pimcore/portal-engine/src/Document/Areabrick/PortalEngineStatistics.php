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

namespace Pimcore\Bundle\PortalEngineBundle\Document\Areabrick;

use Pimcore\Bundle\PortalEngineBundle\Document\AbstractAreabrick;
use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\TranslatorDomain;
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\AreabrickGroup;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\TranslatorService;
use Pimcore\Bundle\StatisticsExplorerBundle\Entity\Configuration;
use Pimcore\Bundle\StatisticsExplorerBundle\Service\ConfigurationLoaderService;
use Pimcore\Model\Document\Editable\Area\Info;

/**
 * Class PortalEngineStatistics
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Document\Areabrick
 */
class PortalEngineStatistics extends AbstractAreabrick
{
    /** @var ConfigurationLoaderService */
    protected $configurationLoaderService;
    /** @var TranslatorService */
    protected $translatorService;

    /**
     * PortalEngineStatistics constructor.
     *
     * @param ConfigurationLoaderService $configurationLoaderService
     * @param TranslatorService $translatorService
     */
    public function __construct(ConfigurationLoaderService $configurationLoaderService, TranslatorService $translatorService)
    {
        $this->configurationLoaderService = $configurationLoaderService;
        $this->translatorService = $translatorService;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Statistics';
    }

    /**
     * @return string|null
     */
    public function getGroup()
    {
        return AreabrickGroup::CONTENT;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return '/bundles/pimcoreadmin/img/flat-color-icons/bar_chart.svg';
    }

    /**
     * {@inheritdoc}
     */
    public function action(Info $info)
    {
        /** @var array $configurations */
        $configurations = $this->configurationLoaderService->loadConfigurationsForActiveUser('portal');
        /** @var Configuration[] $globalConfigurations */
        $globalConfigurations = array_key_exists('global ', $configurations)
            ? $configurations['global ']
            : [];

        /** @var array $statisticSelectConfigurations */
        $statisticSelectConfigurations = [];
        /** @var array $statisticTranslatedHeadline */
        $statisticTranslatedHeadline = [];

        foreach ($globalConfigurations as $globalConfiguration) {
            $translatedName = $this->translatorService->translate($globalConfiguration->getName(), TranslatorDomain::DOMAIN_STATISTIC_NAME);
            $statisticSelectConfigurations[] = [$globalConfiguration->getId(), $translatedName];
            $statisticTranslatedHeadline[$globalConfiguration->getId()] = $translatedName;
        }

        $info->setParam('statisticSelectConfigurations', $statisticSelectConfigurations);
        $info->setParam('statisticTranslatedHeadline', $statisticTranslatedHeadline);
    }
}
