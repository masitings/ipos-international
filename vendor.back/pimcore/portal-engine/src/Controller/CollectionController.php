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

use Pimcore\Bundle\PortalEngineBundle\Entity\Collection;
use Pimcore\Bundle\PortalEngineBundle\Enum\DependencyInjection\ContainerParameter;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Collection\CollectionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Content\HeadTitleService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CollectionController
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Controller
 */
class CollectionController extends AbstractSiteController
{
    /** @var DataPoolConfigService */
    protected $dataPoolConfigService;
    /** @var CollectionService */
    protected $collectionService;

    /**
     * CollectionController constructor.
     *
     * @param DataPoolConfigService $dataPoolConfigService
     * @param CollectionService $collectionService
     */
    public function __construct(DataPoolConfigService $dataPoolConfigService, CollectionService $collectionService)
    {
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->collectionService = $collectionService;
    }

    /**
     * @Route("/{_portal_engine_prefix}collection/list", condition="request.attributes.get('isPortalEngineSite')", name="pimcore_portalengine_collection_list")
     */
    public function listAction(Request $request, HeadTitleService $headTitleService, TranslatorInterface $translator): Response
    {
        $headTitleService->setTitle($translator->trans('portal-engine.content.title.collection-list'));

        return $this->renderTemplate(
            '@PimcorePortalEngine/collection/list.html.twig'
        );
    }

    /**
     * @Route("/{_portal_engine_prefix}collection/{collectionId}", condition="request.attributes.get('isPortalEngineSite')", name="pimcore_portalengine_collection_detail", requirements={"collectionId"="\d+"})
     */
    public function detailAction(Request $request, HeadTitleService $headTitleService, $collectionId): Response
    {
        /** @var int $activeDataPoolId */
        $activeDataPoolId = $request->query->get('activeDataPoolId', 0);
        /** @var string|null $activeDataPoolType */
        $activeDataPoolType = null;
        /** @var Collection $collection */
        $collection = $this->collectionService->getCollectionById($collectionId);
        /** @var DataPoolConfigInterface[] $dataPoolConfigs */
        $dataPoolConfigs = $this->collectionService->getDataPoolConfigsByCollection($collection, true);

        if (!$collection) {
            throw new NotFoundHttpException('Collection not found.');
        }

        try {

            //use first dataPoolId from first collectionItem of selected collection as fallback
            if (!$activeDataPoolId && sizeof($dataPoolConfigs)) {
                $activeDataPoolId = reset($dataPoolConfigs)->getLanguageVariantDataPoolId();
            }

            /** @var DataPoolConfigInterface|null $activeDataPoolConfig */
            $activeDataPoolConfig = $this->dataPoolConfigService->getDataPoolConfigById($activeDataPoolId);

            if (!$activeDataPoolConfig) {
                throw new \Exception('activeDataPoolConfig not found');
            }

            $this
                ->dataPoolConfigService
                ->setCurrentDataPoolConfig($activeDataPoolConfig);

            $activeDataPoolType = $activeDataPoolConfig->getElementType();
        } catch (\Exception $e) {
            //nothing to do
        }

        $headTitleService->setTitle($collection->getName());

        return $this->renderTemplate(
            '@PimcorePortalEngine/collection/detail.html.twig',
            [
                'collection' => $collection,
                'dataPoolConfigs' => $dataPoolConfigs,
                'activeDataPoolType' => $activeDataPoolType,
                'activeDataPoolId' => $activeDataPoolId,
                'selectAllMaxSize' => $this->getParameter(ContainerParameter::SELECT_ALL_MAX_SIZE)
            ]
        );
    }
}
