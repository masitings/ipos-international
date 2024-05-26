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

use Pimcore\Bundle\PortalEngineBundle\Controller\AbstractSiteController;
use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\FilterType;
use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\SortDirection;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\ThumbnailService;
use Pimcore\Bundle\PortalEngineBundle\Service\Content\HeadTitleService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\ClassDefinitionService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\CustomLayoutService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\NameExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormatHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Enum\EnumService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\ListHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\Search\WorkspaceService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\SearchService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\PreConditionService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\TagService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\LanguagesService;
use Pimcore\Controller\KernelControllerEventInterface;
use Pimcore\Http\Request\Resolver\EditmodeResolver;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Tool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DataObjectController extends AbstractSiteController implements KernelControllerEventInterface
{
    public function onKernelControllerEvent(ControllerEvent $event)
    {
        parent::onKernelControllerEvent($event);
        if (!Tool::isFrontendRequestByAdmin()) {
            $this->denyAccessUnlessGranted(Permission::DATA_POOL_ACCESS);
        }
    }

    /**
     * @Route("/data-object/list", name="pimcore_portalengine_data_object_list")
     *
     * @throws \Exception
     */
    public function listAction(
        Request $request,
        DataPoolConfigService $dataPoolConfigService,
        DataPoolService $dataPoolService,
        EditmodeResolver $editmodeResolver,
        ClassDefinitionService $classDefinitionService,
        CustomLayoutService $customLayoutService,
        ThumbnailService $thumbnailService,
        PreConditionService $preConditionService,
        DownloadFormatHandler $downloadFormatHandler,
        SearchService $searchService,
        WorkspaceService $workspaceService,
        EnumService $enumService,
        TagService $tagService,
        LanguagesService $languagesService,
        ListHandler $listHandler
    ) {
        if ($editmodeResolver->isEditmode($request)) {
            $objectPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig();

            return $this->renderTemplate(
                '@PimcorePortalEngine/data_pool/data_object/editmode_config.html.twig',
                [
                    'dataObjectClassStore' => $classDefinitionService->getClassDefinitionSelectStore(),
                    'preConditionServiceStore' => $preConditionService->getPreConditionServicesSelectStore(),
                    'detailPageLayoutStore' => $customLayoutService->getCustomLayoutsSelectStore((string) $objectPoolConfig->getDataObjectClass()),
                    'downloadThumbnailStore' => $thumbnailService->getImageThumbnailSelectStore(),
                    'downloadFormatsStore' => $downloadFormatHandler->getDownloadFormatServicesSelectStore(),
                    'filterFieldsStore' => $searchService->getFilterableFieldsSelectStore(),
                    'filterTypeStore' => $enumService->getDocumentSelectStore(FilterType::class, 'portal-engine.data-pool.filter-type.'),
                    'sortDirectionStore' => $enumService->getDocumentSelectStore(SortDirection::class, 'portal-engine.data-pool.sort-direction.'),
                    'sortFieldsStore' => $searchService->getSortableFieldsSelectStore(),
                    'gridConfigurationAttributes' => $searchService->getListableFieldsSelectStore(),
                    'tagSelectStore' => $tagService->getTagSelectStore(),
                    'languagesStore' => $languagesService->getSelectStore()
                ]
            );
        }

        $dataPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig();

        if (!$dataPoolConfig instanceof DataObjectConfig) {
            throw new NotFoundHttpException('No valid data object pool config found.');
        }

        $this->denyAccessUnlessGranted(Permission::DATA_POOL_ACCESS);

        $dataPool = $dataPoolService->getDataPoolByConfig($dataPoolConfig);

        $rootPath = $workspaceService->getRootPathFromWorkspaces($dataPoolConfig->getWorkspaces());
        $rootName = $workspaceService->getRootNameFromRootPath($rootPath);

        return $this->renderTemplate(
            '@PimcorePortalEngine/data_pool/data_object/list.html.twig',
            [
                'page' => $request->query->get('page', 1),
                'dataPoolId' => $dataPoolConfig->getLanguageVariantDataPoolId(),
                'tagsActive' => $dataPoolConfig->getEnableTagNavigation() && $dataPool->getSearchService()->hasTags(),
                'foldersActive' => $dataPoolConfig->getEnableFolderNavigation(),
                'rootPath' => $rootPath,
                'rootName' => $rootName,
                'selectAllMaxSize' => $listHandler->getSelectAllMaxSize(),
            ]
        );
    }

    /**
     * @Route("/data-object/detail/{id}",
     *     name="pimcore_portalengine_data_object_detail_generic",
     *     requirements={
     *          "id": "\d+"
     *     })
     *
     * @Route("/{documentPath}/o~{id}",
     *     name="pimcore_portalengine_data_object_detail",
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
        NameExtractorService $nameExtractorService
    ) {
        $id = $request->get('id');
        $dataObject = AbstractObject::getById($id);

        if (!$dataObject instanceof Concrete) {
            throw new NotFoundHttpException(sprintf('Data Object ID %s not found', $id));
        }

        $this->denyAccessUnlessGranted(Permission::VIEW, $dataObject);

        $dataPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig();

        if (!$dataPoolConfig instanceof DataObjectConfig) {
            throw new NotFoundHttpException('No valid data object pool config found.');
        }

        $headTitleService->setTitle($nameExtractorService->extractName($dataObject));

        return $this->renderTemplate(
            '@PimcorePortalEngine/data_pool/data_object/detail.html.twig',
            [
                'dataObject' => $dataObject,
                'dataPoolId' => $dataPoolConfig->getLanguageVariantDataPoolId(),
                'enableVersionHistory' => $authorizationChecker->isGranted(Permission::VERSION_HISTORY),
            ]
        );
    }
}
