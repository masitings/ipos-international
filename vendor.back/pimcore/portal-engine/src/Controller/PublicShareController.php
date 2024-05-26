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

namespace Pimcore\Bundle\PortalEngineBundle\Controller;

use Pimcore\Bundle\PortalEngineBundle\Entity\PublicShare;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Content\HeadTitleService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\NameExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\Search\WorkspaceService;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PublicShareController
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Controller
 */
class PublicShareController extends AbstractSiteController
{
    /**
     * @Route("/{_portal_engine_prefix}public-share/list", condition="request.attributes.get('isPortalEngineSite')", name="pimcore_portalengine_public_share_list")
     */
    public function listAction(Request $request, HeadTitleService $headTitleService, TranslatorInterface $translator): Response
    {
        $headTitleService->setTitle($translator->trans('portal-engine.content.title.public-share-list'));

        return $this->renderTemplate(
            '@PimcorePortalEngine/public_share/list.html.twig'
        );
    }

    /**
     * @Route("/{_portal_engine_prefix}public-share/list/{publicShareHash}", condition="request.attributes.get('isPortalEngineSite')", name="pimcore_portalengine_public_share_public_list")
     */
    public function publicListAction(Request $request, PublicShareService $publicShareService, DataPoolConfigService $dataPoolConfigService, HeadTitleService $headTitleService, $publicShareHash): Response
    {
        /** @var PublicShare $publicShare */
        $publicShare = $publicShareService->validateByHash($publicShareHash);
        /** @var int $activeDataPoolId */
        $activeDataPoolId = $request->query->get('activeDataPoolId', 0);
        /** @var string|null $activeDataPoolType */
        $activeDataPoolType = null;
        /** @var DataPoolConfigInterface[] $dataPoolConfigs */
        $dataPoolConfigs = $publicShareService->getDataPoolConfigsByPublicShare($publicShare);

        try {
            //use first dataPoolId from first collectionItem of selected collection as fallback
            if (!$activeDataPoolId && sizeof($dataPoolConfigs)) {
                $activeDataPoolId = reset($dataPoolConfigs)->getId();
            }

            /** @var DataPoolConfigInterface|null $activeDataPoolConfig */
            $activeDataPoolConfig = $dataPoolConfigService->getDataPoolConfigById($activeDataPoolId);

            if (!$activeDataPoolConfig) {
                throw new \Exception('activeDataPoolConfig not found');
            }

            $dataPoolConfigService->setCurrentDataPoolConfig($activeDataPoolConfig);

            $activeDataPoolType = $activeDataPoolConfig->getElementType();
        } catch (\Exception $e) {
            //nothing to do
        }

        $headTitleService->setTitle($publicShare->getName());

        return $this->renderTemplate(
            '@PimcorePortalEngine/public_share/public_list.html.twig',
            [
                'publicShare' => $publicShare,
                'dataPoolConfigs' => $dataPoolConfigs,
                'activeDataPoolType' => $activeDataPoolType,
                'activeDataPoolId' => $activeDataPoolId,
                'showDataPoolTabs' => count($dataPoolConfigs) > 1,
                'showTermsText' => $publicShare->isShowTermsText(),
                'termsText' => $publicShare->getTermsText(),
            ]
        );
    }

    /**
     * @Route("/{documentPath}/{publicShareHash}/public-share-a~{id}", condition="request.attributes.get('isPortalEngineSite')", name="pimcore_portalengine_public_share_public_asset_detail", requirements={"id": "\d+", "documentPath": ".*"})
     */
    public function publicAssetDetailAction(Request $request, PublicShareService $publicShareService, DataPoolConfigService $dataPoolConfigService, HeadTitleService $headTitleService, NameExtractorService $nameExtractorService, WorkspaceService $workspaceService, $publicShareHash): Response
    {
        /** @var PublicShare $publicShare */
        $publicShare = $publicShareService->validateByHash($publicShareHash);
        /** @var int|null $id */
        $id = $request->get('id');
        /** @var Asset|null $asset */
        $asset = Asset::getById($id);
        if (!$asset) {
            throw new NotFoundHttpException(sprintf('Asset ID %s not found', $id));
        }
        if (!$publicShareService->isElementInPublicShare($publicShare, $asset)) {
            throw new NotFoundHttpException('Element not in PublicShare.');
        }

        /** @var DataPoolConfigInterface|null $dataPoolConfig */
        $dataPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig();
        if (!$dataPoolConfig instanceof AssetConfig) {
            throw new NotFoundHttpException('No valid data pool config found.');
        }

        $headTitleService->setTitle($nameExtractorService->extractName($asset));

        $rootPath = $workspaceService->getRootPathFromWorkspaces($dataPoolConfig->getWorkspaces());
        $rootName = $workspaceService->getRootNameFromRootPath($rootPath);

        return $this->renderTemplate(
            '@PimcorePortalEngine/public_share/public_asset_detail.html.twig',
            [
                'asset' => $asset,
                'dataPoolId' => $dataPoolConfig->getId(),
                'rootPath' => $rootPath,
                'rootName' => $rootName,
                'enableVersionHistory' => false,
                'publicShare' => $publicShare,
                'showTermsText' => $publicShare->isShowTermsText(),
                'termsText' => $publicShare->getTermsText(),
            ]
        );
    }

    /**
     * @Route("/{documentPath}/{publicShareHash}/public-share-o~{id}", condition="request.attributes.get('isPortalEngineSite')", name="pimcore_portalengine_public_share_public_object_detail", requirements={"id": "\d+", "documentPath": ".*"})
     */
    public function publicDataObjectDetailAction(Request $request, PublicShareService $publicShareService, DataPoolConfigService $dataPoolConfigService, HeadTitleService $headTitleService, NameExtractorService $nameExtractorService, $publicShareHash): Response
    {
        /** @var PublicShare $publicShare */
        $publicShare = $publicShareService->validateByHash($publicShareHash);
        /** @var int|null $id */
        $id = $request->get('id');
        /** @var AbstractObject|null $dataObject */
        $dataObject = AbstractObject::getById($id);
        if (!$dataObject instanceof Concrete) {
            throw new NotFoundHttpException(sprintf('Data Object ID %s not found', $id));
        }
        if (!$publicShareService->isElementInPublicShare($publicShare, $dataObject)) {
            throw new NotFoundHttpException('Element not in PublicShare.');
        }

        /** @var DataPoolConfigInterface|null $dataPoolConfig */
        $dataPoolConfig = $dataPoolConfigService->getCurrentDataPoolConfig();
        if (!$dataPoolConfig instanceof DataObjectConfig) {
            throw new NotFoundHttpException('No valid data pool config found.');
        }

        $headTitleService->setTitle($nameExtractorService->extractName($dataObject));

        return $this->renderTemplate(
            '@PimcorePortalEngine/public_share/public_data_object_detail.html.twig',
            [
                'dataObject' => $dataObject,
                'dataPoolId' => $dataPoolConfig->getId(),
                'enableVersionHistory' => false,
                'publicShare' => $publicShare,
                'showTermsText' => $publicShare->isShowTermsText(),
                'termsText' => $publicShare->getTermsText(),
            ]
        );
    }
}
