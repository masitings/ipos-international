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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider;

use Pimcore\Bundle\PortalEngineBundle\Enum\Download\Type;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadFormatAccess;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadType;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\TranslatorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\Asset;

class AssetThumbnailsProvider implements DownloadProviderInterface
{
    use ThumbnailProviderTrait;

    protected $downloadService;
    protected $permissionService;
    protected $securityService;

    public function __construct(
        DownloadService $downloadService,
        TranslatorService $translatorService,
        PermissionService $permissionService,
        SecurityService $securityService
    ) {
        $this->downloadService = $downloadService;
        $this->translatorService = $translatorService;
        $this->permissionService = $permissionService;
        $this->securityService = $securityService;
    }

    /**
     * {@inheritDoc}
     */
    public function canProvide(DataPoolConfigInterface $config): bool
    {
        return $config instanceof AssetConfig && $config->isEnabled();
    }

    /**
     * @param AssetConfig $config
     * @param bool $checkPermissions
     *
     * @return DownloadType[]
     */
    public function provide(DataPoolConfigInterface $config, bool $checkPermissions = true): array
    {
        $downloadType = new DownloadType();
        $downloadType
            ->setType(Type::ASSET)
            ->setLabel($this->downloadService->getLabelForDownloadable($config, Type::ASSET));

        $this->addThumbnailsToDownloadType($config, $downloadType);

        if ($checkPermissions) {
            $formats = $downloadType->getFormats();
            $allowedFormats = [];
            foreach ($formats as $format) {
                $downloadFormatAccess = DownloadFormatAccess::fromDownloadTypeAndFormat(
                    $config->getId(),
                    $downloadType,
                    $format['id']
                );
                if ($this->permissionService->isAllowed(
                    $this->securityService->getPortalUser(),
                    $downloadFormatAccess->toPermission()
                )) {
                    $allowedFormats[] = $format;
                }
            }
            $downloadType->setFormats($allowedFormats);
            if (empty($allowedFormats)) {
                return [];
            }
        }

        return [$downloadType];
    }

    /**
     * {@inheritDoc}
     */
    public function canExtractSource(DataPoolConfigInterface $dataPoolConfig, DownloadConfig $downloadConfig, $source): bool
    {
        return
            $this->canProvide($dataPoolConfig) &&
            $downloadConfig->getType() !== Type::STRUCTURED_DATA &&
            $source instanceof Asset;
    }

    /**
     * {@inheritDoc}
     */
    public function extractSource(DataPoolConfigInterface $dataPoolConfig, DownloadConfig $downloadConfig, $source)
    {
        return $source;
    }
}
