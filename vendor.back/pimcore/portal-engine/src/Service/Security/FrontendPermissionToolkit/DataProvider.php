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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Security\FrontendPermissionToolkit;

use FrontendPermissionToolkitBundle\CoreExtensions\ClassDefinitions\Interfaces\DataProviderInterface;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadAccess;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadFormatAccess;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadProviderService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\AssetThumbnailsProvider;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\DownloadProviderInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\StructuredDataProvider;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\Site;

class DataProvider implements DataProviderInterface
{
    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var DownloadProviderService
     */
    protected $downloadProviderService;

    /**
     * DataProvider constructor.
     *
     * @param PortalConfigService $portalConfigService
     * @param DataPoolConfigService $dataPoolConfigService
     * @param DownloadProviderService $downloadProviderService
     */
    public function __construct(PortalConfigService $portalConfigService, DataPoolConfigService $dataPoolConfigService, DownloadProviderService $downloadProviderService)
    {
        $this->portalConfigService = $portalConfigService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->downloadProviderService = $downloadProviderService;
    }

    /**
     * @param array $context
     * @param Data $fieldDefinition
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getPermissionResources(array $context, Data $fieldDefinition): array
    {
        $result = [];
        foreach ($this->portalConfigService->getAllPortalConfigs() as $portalConfig) {
            $result[] = [
                'value' => Permission::PORTAL_ACCESS . Permission::PERMISSION_DELIMITER . $portalConfig->getPortalId(),
                'label' => '<strong style="font-size:1.5em;">' . $portalConfig->getPortalName() . '</strong>',
            ];

            if ($site = Site::getByRootId($portalConfig->getPortalId())) {
                $result[] = [
                    'value' => Permission::STATISTIC_EXPLORER_ACCESS . Permission::PERMISSION_DELIMITER . $portalConfig->getPortalId(),
                    'label' => '<span style="color: #999;">' . $portalConfig->getPortalName() . ' - Statistic Explorer</span>'
                ];

                foreach ($this->dataPoolConfigService->getDataPoolConfigsFromSite($site) as $dataPoolConfig) {
                    $dataPoolLabel = $portalConfig->getPortalName() . ' - Data Pool ' . $dataPoolConfig->getDataPoolName();
                    $result[] = [
                        'value' => Permission::DATA_POOL_ACCESS . Permission::PERMISSION_DELIMITER . $dataPoolConfig->getId(),
                        'label' => '<strong>' . $dataPoolLabel . '</strong>'
                    ];

                    foreach ($this->downloadProviderService->getProviders() as $downloadProvider) {
                        if (!$downloadProvider->canProvide($dataPoolConfig)) {
                            continue;
                        }
                        $downloadTypes = $downloadProvider->provide($dataPoolConfig, false);
                        foreach ($downloadTypes as $downloadType) {
                            $downloadPermission = DownloadAccess::fromDownloadType($dataPoolConfig->getId(), $downloadType);

                            if ($this->shouldAppendFormatPermissions($dataPoolConfig, $downloadProvider, $downloadTypes)) {
                                foreach ($downloadType->getFormats() as $format) {
                                    $permission = DownloadFormatAccess::fromDownloadTypeAndFormat(
                                        $dataPoolConfig->getId(),
                                        $downloadType,
                                        $format['id']
                                    );

                                    $result[] = [
                                        'value' => $permission->toPermission(),
                                        'label' => '<span style="color: #999;">'.$dataPoolLabel.' -  Download ' . $downloadType->getLabel() . ' - Format '.$format['label'].'</span>'
                                    ];
                                }
                            } else {
                                $result[] = [
                                    'value' => $downloadPermission->toPermission(),
                                    'label' => '<span style="color: #999;">' . $dataPoolLabel . ' - Download ' . $downloadType->getLabel() . '</span>'
                                ];
                            }
                        }
                    }
                    if ($dataPoolConfig instanceof AssetConfig) {
                        $result[] = [
                            'value' => Permission::DATA_POOL_ASSET_UPLOAD_FOLDER_REVIEWING . Permission::PERMISSION_DELIMITER . $dataPoolConfig->getId(),
                            'label' => '<span style="color: #999;">' . $dataPoolLabel . ' - Asset Upload Folder Reviewing</span>'
                        ];
                    }

                    $result[] = [
                        'value' => Permission::VERSION_HISTORY . Permission::PERMISSION_DELIMITER . $dataPoolConfig->getId(),
                        'label' => '<span style="color: #999;">' . $dataPoolLabel . ' - Version History</span>'
                    ];
                }
            }
        }

        return $result;
    }

    protected function shouldAppendFormatPermissions(DataPoolConfigInterface $dataPoolConfig, DownloadProviderInterface $downloadProvider, array $downloadTypes)
    {
        if ($dataPoolConfig instanceof AssetConfig
            && $downloadProvider instanceof AssetThumbnailsProvider
            && $downloadType = array_pop($downloadTypes)) {
            return true;
        }

        if ($downloadProvider instanceof StructuredDataProvider) {
            return true;
        }

        return false;
    }
}
