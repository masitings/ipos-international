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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\DataPool;

use Pimcore\Bundle\DirectEditBundle\Service\MercureUrlService;
use Pimcore\Bundle\PortalEngineBundle\Controller\AbstractSiteController;
use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\FilterType;
use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\SortDirection;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\AttributeService;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\NameExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\ThumbnailService;
use Pimcore\Bundle\PortalEngineBundle\Service\Content\HeadTitleService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormatHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Enum\EnumService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Asset\ListHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\Search\WorkspaceService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\SearchService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\PreConditionService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\TagService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\DirectEditPermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\LanguagesService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Http\Request\Resolver\EditmodeResolver;
use Pimcore\Model\Asset;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AssetController extends AbstractSiteController
{
    /**
     * @throws \Exception
     */
    public function listAction(
        Request $request,
        DataPoolConfigService $dataPoolConfigService,
        DataPoolService $dataPoolService,
        EditmodeResolver $editmodeResolver,
        PreConditionService $preConditionService,
        ThumbnailService $thumbnailService,
        DownloadFormatHandler $downloadFormatHandler,
        EnumService $enumService,
        TagService $tagService,
        SearchService $searchService,
        WorkspaceService $workspaceService,
        AttributeService $attributeService,
        LanguagesService $languagesService,
        ListHandler $listHandler
    ) {
        if ($editmodeResolver->isEditmode($request)) {
            return $this->renderTemplate(
                '@PimcorePortalEngine/data_pool/asset/editmode_config.html.twig',
                [
                    'preConditionServiceStore' => $preConditionService->getPreConditionServicesSelectStore(),
                    'downloadThumbnailStore' => $thumbnailService->getImageThumbnailSelectStore(),
                    'downloadFormatsStore' => $downloadFormatHandler->getDownloadFormatServicesSelectStore(),
                    'filterFieldsStore' => $searchService->getFilterableFieldsSelectStore(),
                    'filterTypeStore' => $enumService->getDocumentSelectStore(FilterType::class, 'portal-engine.data-pool.filter-type.'),
                    'sortDirectionStore' => $enumService->getDocumentSelectStore(SortDirection::class, 'portal-engine.data-pool.sort-direction.'),
                    'sortFieldsStore' => $searchService->getSortableFieldsSelectStore(),
                    'gridConfigurationAttributes' => $searchService->getListableFieldsSelectStore(),
                    'attributes' => $attributeService->getAllAttributesStore(),
                    'tagSelectStore' => $tagService->getTagSelectStore(),
                    'languagesStore' => $languagesService->getSelectStore()
                ]
            );
        }

        $dataPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig();

        if (!$dataPoolConfig instanceof AssetConfig) {
            throw new NotFoundHttpException('No valid asset data pool config found.');
        }

        $this->denyAccessUnlessGranted(Permission::DATA_POOL_ACCESS);

        $dataPool = $dataPoolService->getDataPoolByConfig($dataPoolConfig);

        $rootPath = $workspaceService->getRootPathFromWorkspaces($dataPoolConfig->getWorkspaces());
        $rootName = $workspaceService->getRootNameFromRootPath($rootPath);

        /** @var bool $insideUploadFolder */
        $insideUploadFolder = $request->query->get('uploadFolder') === 'true';

        return $this->renderTemplate(
            '@PimcorePortalEngine/data_pool/asset/list.html.twig',
            [
                'dataPoolId' => $dataPoolConfig->getLanguageVariantDataPoolId(),
                'page' => $request->query->get('page', 1),
                'tagsActive' => $dataPoolConfig->getEnableTagNavigation() && $dataPool->getSearchService()->hasTags(),
                'foldersActive' => !$insideUploadFolder && $dataPoolConfig->getEnableFolderNavigation(),
                'rootPath' => $rootPath,
                'rootName' => $rootName,
                'insideUploadFolder' => $insideUploadFolder,
                'selectAllMaxSize' => $listHandler->getSelectAllMaxSize(),
            ]
        );
    }

    /**
     * @Route("/{documentPath}/a~{id}",
     *     name="pimcore_portalengine_asset_detail",
     *     requirements={
     *          "id": "\d+",
     *          "documentPath": ".*"
     *     })
     */
    public function detailAction(
        Request $request,
        DataPoolConfigService $dataPoolConfigService,
        AuthorizationCheckerInterface $authorizationChecker,
        HeadTitleService $headTitleService,
        NameExtractorService $nameExtractorService,
        WorkspaceService $workspaceService,
        SecurityService $securityService
    ) {
        $id = $request->get('id');
        $asset = Asset::getById($id);

        if (empty($asset)) {
            throw new NotFoundHttpException(sprintf('Asset ID %s not found', $id));
        }

        $this->denyAccessUnlessGranted(Permission::VIEW, $asset);

        $dataPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig();

        if (!$dataPoolConfig instanceof AssetConfig) {
            throw new NotFoundHttpException('No valid data object pool config found.');
        }

        $headTitleService->setTitle($nameExtractorService->extractName($asset));

        $rootPath = $workspaceService->getRootPathFromWorkspaces($dataPoolConfig->getWorkspaces());
        $rootName = $workspaceService->getRootNameFromRootPath($rootPath);

        // direct edit initialization
        $mercureUrl = '';
        $directEditUserId = 'DUMMY_PREFIX_' . $securityService->getPortalUser()->getId();
        if (class_exists('Pimcore\\Bundle\\DirectEditBundle\\Service\\MercureUrlService')) {
            $mercureUrlService = \Pimcore::getContainer()->get(MercureUrlService::class);
            if ($mercureUrlService) {
                $mercureUrl = $mercureUrlService->getClientSideUrl();
            }
        }

        if (interface_exists('Pimcore\\Bundle\\DirectEditBundle\\Service\\Permission\\PermissionServiceInterface')) {
            $directEditUserId = DirectEditPermissionService::PREFIX . $securityService->getPortalUser()->getId();
        }

        return $this->renderTemplate(
            '@PimcorePortalEngine/data_pool/asset/detail.html.twig',
            [
                'asset' => $asset,
                'dataPoolId' => $dataPoolConfig->getLanguageVariantDataPoolId(),
                'rootPath' => $rootPath,
                'rootName' => $rootName,
                'enableVersionHistory' => $authorizationChecker->isGranted(Permission::VERSION_HISTORY),
                'directEdit' => [
                    'userId' => $directEditUserId,
                    'mercureUrl' => $mercureUrl
                ]
            ]
        );
    }
}
