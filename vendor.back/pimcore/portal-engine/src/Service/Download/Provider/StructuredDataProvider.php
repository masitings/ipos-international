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
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadFormatAccess;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadType;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormatHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\Element\ElementInterface;

class StructuredDataProvider implements DownloadProviderInterface
{
    protected $downloadService;
    protected $downloadFormatHandler;
    protected $permissionService;
    protected $securityService;

    public function __construct(DownloadService $downloadService, DownloadFormatHandler $downloadFormatHandler, PermissionService $permissionService, SecurityService $securityService)
    {
        $this->downloadService = $downloadService;
        $this->downloadFormatHandler = $downloadFormatHandler;
        $this->permissionService = $permissionService;
        $this->securityService = $securityService;
    }

    /**
     * {@inheritDoc}
     */
    public function canProvide(DataPoolConfigInterface $config): bool
    {
        return $config->isEnabled() && sizeof($config->getAvailableDownloadFormats());
    }

    /**
     * @param DataObjectConfig $config
     * @param bool $checkPermissions
     *
     * @return DownloadType[]
     */
    public function provide(DataPoolConfigInterface $config, bool $checkPermissions = true): array
    {
        $downloadType = new DownloadType();
        $downloadType
            ->setType(Type::STRUCTURED_DATA)
            ->setLabel($this->downloadService->getLabelForDownloadable($config, Type::STRUCTURED_DATA));

        foreach ($config->getAvailableDownloadFormats() as $id) {
            $format = $this->downloadFormatHandler->getDownloadFormatService($id);

            if (!$format || !$format->supports($config)) {
                continue;
            }

            $addFormat = true;
            if ($checkPermissions) {
                $downloadFormatAccess = DownloadFormatAccess::fromDownloadTypeAndFormat(
                    $config->getId(),
                    $downloadType,
                    $id
                );

                $addFormat = $this->permissionService->isAllowed(
                    $this->securityService->getPortalUser(),
                    $downloadFormatAccess->toPermission()
                );
            }

            if ($addFormat) {
                $downloadType->addFormat($id, $format->getDisplayName());
            }
        }

        if (empty($downloadType->getFormats())) {
            return [];
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
            $downloadConfig->getType() === Type::STRUCTURED_DATA &&
            $source instanceof ElementInterface;
    }

    /**
     * @param DataObjectConfig $dataPoolConfig
     * @param DownloadConfig $downloadConfig
     * @param ElementInterface $source
     *
     * @return mixed
     */
    public function extractSource(DataPoolConfigInterface $dataPoolConfig, DownloadConfig $downloadConfig, $source)
    {
        // for structured data, use the object itself as source
        return $source;
    }
}
