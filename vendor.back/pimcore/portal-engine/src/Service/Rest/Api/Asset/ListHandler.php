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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Asset;

use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataPool\ListData;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataPool\ListDataEntry;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\FileNameParserService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataPool\AbstractListHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\SearchService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\FolderService;
use Pimcore\Model\DataObject\PortalUser;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ListHandler
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Asset
 */
class ListHandler extends AbstractListHandler
{
    /** @var DataPoolConfigService */
    protected $dataPoolConfigService;
    /** @var FolderService */
    protected $folderService;
    /** @var FileNameParserService */
    protected $fileNameParserService;

    /**
     * @param DataPoolConfigService $dataPoolConfigService
     * @required
     */
    public function setDataPoolConfigService(DataPoolConfigService $dataPoolConfigService)
    {
        $this->dataPoolConfigService = $dataPoolConfigService;
    }

    /**
     * @param FolderService $folderService
     * @required
     */
    public function setFolderService(FolderService $folderService)
    {
        $this->folderService = $folderService;
    }

    /**
     * @param FileNameParserService $fileNameParserService
     * @required
     */
    public function setFileNameParserService(FileNameParserService $fileNameParserService)
    {
        $this->fileNameParserService = $fileNameParserService;
    }

    /**
     * @param Request $request
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getData(Request $request): array
    {
        /** @var AssetConfig $currentDataPoolConfig */
        $currentDataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig();
        /** @var PortalUser $portalUser */
        $portalUser = $this->securityService->getPortalUser();
        /** @var string $currentFolderFullPath */
        $currentFolderFullPath = $this->folderService->getFolder($request->query->all());
        /** @var bool $uploadFolderAvailable */
        $uploadFolderAvailable = (bool)($request->query->get('uploadFolder') !== 'true' && $currentDataPoolConfig->getUploadFolder() && $this->permissionService->isPermissionAllowed(Permission::CREATE, $portalUser, $currentDataPoolConfig->getId(), $currentDataPoolConfig->getUploadFolder()->getRealFullPath(), false, false, false));

        /** @var array $listData */
        $data = parent::getData($request);

        if ($request->query->get('uploadFolder') === 'true' && $currentDataPoolConfig->getUploadFolder()) {
            $currentFolderFullPath = $currentDataPoolConfig->getUploadFolder()->getRealFullPath();
        }

        $data['currentFolder']['fullPath'] = $currentFolderFullPath;
        $data['currentFolder']['permissions'] = $this->permissionService->getPermissionsForUser(
            $this->securityService->getPortalUser(),
            $currentDataPoolConfig->getId(),
            $currentFolderFullPath,
            true
        );

        $data['uploadFolder'] = $uploadFolderAvailable
            ? $currentDataPoolConfig->getLanguageVariantOrDocument()->getFullPath(). '?uploadFolder=true'
            : null;

        return $data;
    }

    /**
     * @param Request $request
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getFoldersData(Request $request): array
    {
        $params = $request->query->all();
        $params[SearchService::PARAM_INCLUDE_FOLDERS] = true;

        return $this->searchService->getFoldersDataByParams($params);
    }

    /**
     * @param ListData $listData
     * @param ListDataEntry $listDataEntry
     *
     * @return array
     */
    protected function getListItemData(ListData $listData, ListDataEntry $listDataEntry)
    {
        $data = parent::getListItemData($listData, $listDataEntry);
        $data['fileExtension'] = $this->fileNameParserService->getExtensionFromFilename($listDataEntry->getName());

        $data['permissions'] = $this->permissionService->getPermissionsForUser(
            $this->securityService->getPortalUser(),
            $this->dataPoolConfigService->getCurrentDataPoolConfig()->getId(),
            $listDataEntry->getFullPath(),
            $listDataEntry->getHasWorkflowWithPermissions()
        );

        return $data;
    }
}
