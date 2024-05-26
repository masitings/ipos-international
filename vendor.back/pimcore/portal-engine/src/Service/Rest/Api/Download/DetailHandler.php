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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Download;

use Knp\Component\Pager\Event\Subscriber\Paginate\Doctrine\ORM\QuerySubscriber;
use Knp\Component\Pager\PaginatorInterface;
use Pimcore\Bundle\PortalEngineBundle\Entity\DownloadCart;
use Pimcore\Bundle\PortalEngineBundle\Entity\DownloadCartItem;
use Pimcore\Bundle\PortalEngineBundle\Enum\Download\CartMessage;
use Pimcore\Bundle\PortalEngineBundle\Enum\ImageThumbnails;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadAccess;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\Download\DownloadList;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\Download\DownloadListEntry;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\ThumbnailService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Document\LanguageVariantService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadCartService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\NameExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DetailHandler
{
    protected $nameExtractorService;
    protected $thumbnailService;
    protected $urlExtractorService;
    protected $dataPoolConfigService;
    protected $downloadCartService;
    protected $downloadService;
    protected $authorizationChecker;
    protected $router;
    protected $languageVariantService;
    protected $paginator;

    protected $itemCountPerPage = 50;

    public function __construct(
        NameExtractorService $nameExtractorService,
        ThumbnailService $thumbnailService,
        UrlExtractorService $urlExtractorService,
        DataPoolConfigService $dataPoolConfigService,
        DownloadCartService $downloadCartService,
        DownloadService $downloadService,
        AuthorizationCheckerInterface $authorizationChecker,
        RouterInterface $router,
        LanguageVariantService $languageVariantService,
        PaginatorInterface $paginator
    ) {
        $this->nameExtractorService = $nameExtractorService;
        $this->thumbnailService = $thumbnailService;
        $this->urlExtractorService = $urlExtractorService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->downloadCartService = $downloadCartService;
        $this->downloadService = $downloadService;
        $this->authorizationChecker = $authorizationChecker;
        $this->router = $router;
        $this->languageVariantService = $languageVariantService;
        $this->paginator = $paginator;
    }

    /**
     * @param Request $request
     *
     * @return DownloadConfig[]
     */
    public function getDownloadConfigsFromRequest(Request $request)
    {
        try {
            $rawConfigs = json_decode($request->getContent(), true);
            $rawConfigs = $rawConfigs['configs'];

            $configs = [];

            if (!empty($rawConfigs)) {
                foreach ($rawConfigs as $rawConfig) {
                    $config = new DownloadConfig();
                    $config->add($rawConfig);

                    $configs[] = $config;
                }
            }

            // check permission for adding the given download configs
            $configs = $this->filterUnauthorizedDownloadConfigs($configs, $this->dataPoolConfigService->getCurrentDataPoolConfig());

            return $configs;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param Request $request
     *
     * @return ElementInterface[]
     */
    public function getSelectedElementsFromRequest(Request $request)
    {
        $selectedIds = $request->get('selectedIds');
        $elements = [];

        if (!empty($selectedIds)) {
            foreach ($selectedIds as $selectedId) {
                $elements[] = $this->dataPoolConfigService->getElementById($selectedId);
            }
        }

        return $elements;
    }

    /**
     * @param Request $request
     * @param DownloadCart $downloadCart
     *
     * @return DownloadList
     *
     * @throws \Exception
     */
    public function getData(Request $request)
    {
        $countQuery = $this->downloadCartService->getDownloadCartItemsQuery();
        $count = $countQuery->select('count(identity(dci))')->getQuery()->getSingleScalarResult();
        $query = $this->downloadCartService->getDownloadCartItemsQuery();
        $query = $query->getQuery()
            ->setHint('knp_paginator.count', $count)
            ->setHint(QuerySubscriber::HINT_FETCH_JOIN_COLLECTION, false);

        $pagination = $this->paginator->paginate($query, $request->get('page', 1), $this->itemCountPerPage);

        return DownloadList::createFromPagination($pagination, [$this, 'getDownloadCartItemData'], $this->router->generate('pimcore_portalengine_download_cart_detail'))
            ->setCartId($this->downloadCartService->getDownloadCart()->getId());
    }

    /**
     * @param DownloadCartItem $downloadCartItem
     *
     * @return DownloadListEntry|null
     */
    public function getDownloadCartItemData(DownloadCartItem $downloadCartItem)
    {
        $element = $downloadCartItem->getElement();

        if (!$element) {
            return null;
        }

        $configs = $downloadCartItem->getConfigs();

        $this->dataPoolConfigService->setCurrentDataPoolConfigById($downloadCartItem->getDataPoolId());
        $dataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig();

        if (empty($dataPoolConfig)) {
            return null;
        }

        if (!empty($configs)) {
            foreach ($configs as $config) {
                $config->setLabel($this->downloadService->getLabelForDownloadable(
                    $dataPoolConfig,
                    $config->getType(),
                    $config->getAttribute()
                ));
            }
        }

        $messages = [];
        if (empty($this->downloadService->getDownloadablesFromDownloadItem($downloadCartItem))) {
            $messages[] = CartMessage::NO_DOWNLOADABLE_ITEM;
        }
        if (!$this->authorizationChecker->isGranted(Permission::DOWNLOAD, $element)) {
            return null;
        }

        $dataPoolId = $downloadCartItem->getDataPoolId();
        $document = Page::getById($downloadCartItem->getDataPoolId());
        if ($document) {
            $document = $this->languageVariantService->getLanguageVariant($document);
            $dataPoolId = $document ? $document->getId() : $dataPoolId;
        }

        $this->dataPoolConfigService->setCurrentDataPoolConfigById($dataPoolId);
        $dataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig();

        return (new DownloadListEntry())
            ->setId($this->downloadCartService->createItemKey($downloadCartItem))
            ->setDataPoolId($downloadCartItem->getDataPoolId())
            ->setDataPoolName($dataPoolConfig->getDataPoolName())
            ->setName($this->nameExtractorService->extractName($element))
            ->setThumbnail($this->thumbnailService->getThumbnailPathFromElement($element, ImageThumbnails::ELEMENT_TEASER))
            ->setDetailLink($this->urlExtractorService->extractUrl($element, $dataPoolConfig))
            ->setConfigs($this->transformDownloadCartItemConfigs($downloadCartItem->getConfigs()))
            ->setMessages($messages);
    }

    /**
     * @param DownloadConfig[] $configs
     *
     * @return array
     */
    protected function transformDownloadCartItemConfigs(array $configs)
    {
        $result = [];
        foreach ($configs as $config) {
            $data = $config->jsonSerialize();
            $data['formatLabel'] = $this->downloadService->getFormatLabelForDownloadType($data['type'], $data['format']);
            $result[] = $data;
        }

        return $result;
    }

    /**
     * @param DownloadConfig[] $configs
     * @param DataPoolConfigInterface $dataPoolConfig
     */
    protected function filterUnauthorizedDownloadConfigs(array $configs, DataPoolConfigInterface $dataPoolConfig)
    {
        // check permission for adding the given download configs
        $configs = array_filter($configs, function (DownloadConfig $config) use ($dataPoolConfig) {
            return $this->authorizationChecker->isGranted(Permission::DOWNLOAD, DownloadAccess::fromDownloadConfig($dataPoolConfig->getId(), $config));
        });

        return $configs;
    }
}
