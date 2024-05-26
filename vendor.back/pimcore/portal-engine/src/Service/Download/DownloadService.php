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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download;

use Pimcore\Bundle\PortalEngineBundle\Enum\Download\Type;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItemInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormatHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Generator\DownloadGeneratorInterface;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DownloadService
{
    const ZOMBIE_DOWNLOAD_PREFIX = 'portal-engine-download__';

    protected $dataPoolConfigService;
    protected $downloadProviderService;
    protected $authorizationChecker;
    protected $downloadFormatHandler;

    /**
     * @var DownloadGeneratorInterface[]
     */
    protected $generators = [];

    public function __construct(DataPoolConfigService $dataPoolConfigService, DownloadProviderService $downloadProviderService, AuthorizationCheckerInterface $authorizationChecker, DownloadFormatHandler $downloadFormatHandler)
    {
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->downloadProviderService = $downloadProviderService;
        $this->authorizationChecker = $authorizationChecker;
        $this->downloadFormatHandler = $downloadFormatHandler;
    }

    /**
     * @param DownloadGeneratorInterface $downloadGenerator
     */
    public function addDownloadGenerator(DownloadGeneratorInterface $downloadGenerator)
    {
        $this->generators[] = $downloadGenerator;
    }

    /**
     * @param DownloadItemInterface $downloadCartItem
     * @param string|null $downloadUniqid
     *
     * @return DownloadableInterface[]
     */
    public function getDownloadablesFromDownloadItem(DownloadItemInterface $downloadCartItem, string $downloadUniqid = null)
    {
        if (empty($downloadCartItem->getConfigs())) {
            return [];
        }

        $dataPoolConfig = $this->dataPoolConfigService->getDataPoolConfigById($downloadCartItem->getDataPoolId());

        if (!$dataPoolConfig) {
            return [];
        }

        $element = $downloadCartItem->getElement();

        if (!$element) {
            return [];
        }

        return $this->getDownloadablesFromElement($dataPoolConfig, $element, $downloadCartItem->getConfigs(), $downloadUniqid);
    }

    /**
     * @param DataPoolConfigInterface $config
     * @param string $type
     * @param string|null $attribute
     *
     * @return string|null
     */
    public function getLabelForDownloadable(DataPoolConfigInterface $config, string $type, ?string $attribute = null)
    {
        if ($type === Type::STRUCTURED_DATA && $config instanceof DataObjectConfig) {
            return $config->getDataObjectClass();
        } elseif ($attribute) {
            return $attribute;
        } else {
            return $type;
        }
    }

    /**
     * @param DataPoolConfigInterface $config
     * @param string $type
     * @param string|null $attribute
     *
     * @return string|null
     */
    public function getFormatLabelForDownloadType(string $type, ?string $format = null)
    {
        if ($type === Type::STRUCTURED_DATA && !empty($format) && $this->downloadFormatHandler->getDownloadFormatService($format)) {
            return $this->downloadFormatHandler->getDownloadFormatService($format)->getDisplayName();
        } elseif ($format) {
            return $format;
        } else {
            return $type;
        }
    }

    /**
     * @param ElementInterface $element
     * @param DownloadConfig[] $configs
     *
     * @return DownloadableInterface[]
     */
    public function getDownloadablesFromElement(DataPoolConfigInterface $dataPoolConfig, ElementInterface $element, array $configs, string $downloadUniqid = null)
    {
        $downloadables = [];

        foreach ($configs as $config) {
            $sources = $this->downloadProviderService->getSources($dataPoolConfig, $config, $element);

            foreach ($this->generators as $generator) {
                foreach ($sources as $source) {
                    if (!$generator->supports($source, $config)) {
                        continue;
                    }

                    $downloadable = $generator->createDownloadable($source, $config);
                    $downloadable
                        ->setDataPoolConfig($dataPoolConfig)
                        ->setDownloadConfig($config)
                        ->setLabel($this->getLabelForDownloadable($dataPoolConfig, $config->getType(), $config->getAttribute()))
                        ->setDownloadUniqid($downloadUniqid . '_' . $dataPoolConfig->getId());

                    $downloadables[] = $downloadable;
                }
            }
        }

        return $downloadables;
    }
}
