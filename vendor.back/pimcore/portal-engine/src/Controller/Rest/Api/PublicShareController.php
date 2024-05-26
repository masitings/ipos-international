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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api;

use Knp\Component\Pager\PaginatorInterface;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\PortalEngineBundle\Entity\Collection;
use Pimcore\Bundle\PortalEngineBundle\Entity\PublicShare;
use Pimcore\Bundle\PortalEngineBundle\Entity\PublicShareItem;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElementType;
use Pimcore\Bundle\PortalEngineBundle\Exception\OutputErrorException;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\ApiPayload;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\BasicListJsonModel;
use Pimcore\Bundle\PortalEngineBundle\Service\Collection\CollectionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Document\LanguageVariantService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Localization\IntlFormatter;
use Pimcore\Model\Document;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/public-share", condition="request.attributes.get('isPortalEngineSite')")
 */
class PublicShareController extends AbstractRestApiController
{
    /** @var PublicShareService */
    protected $publicShareService;
    /** @var CollectionService */
    protected $collectionService;
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;
    /** @var IntlFormatter */
    protected $formatter;
    /** @var Document\Service */
    protected $documentService;
    /** @var LanguageVariantService */
    protected $languageVariantService;

    /** @var int */
    protected $itemCountPerPage = 10;

    /**
     * PublicShareController constructor.
     *
     * @param PublicShareService $publicShareService
     * @param CollectionService $collectionService
     * @param UrlGeneratorInterface $urlGenerator
     * @param IntlFormatter $formatter
     * @param Document\Service $documentService
     */
    public function __construct(
        PublicShareService $publicShareService,
        CollectionService $collectionService,
        UrlGeneratorInterface $urlGenerator,
        IntlFormatter $formatter,
        Document\Service $documentService,
        LanguageVariantService $languageVariantService
    ) {
        $this->publicShareService = $publicShareService;
        $this->collectionService = $collectionService;
        $this->urlGenerator = $urlGenerator;
        $this->formatter = $formatter;
        $this->documentService = $documentService;
        $this->languageVariantService = $languageVariantService;
    }

    /**
     * @Route(
     *     "/create",
     *     name="pimcore_portalengine_rest_api_public_share_create"
     * )
     */
    public function createAction(Request $request): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);
        /** @var \stdClass $requestBodyParams */
        $requestBodyParams = json_decode($request->getContent(), false);

        try {
            /** @var string $name */
            $name = trim($requestBodyParams->name);
            /** @var array $downloadConfigs */
            $downloadConfigs = (array)$requestBodyParams->downloadConfigs;
            /** @var int|null $expiryDate */
            $expiryDate = $requestBodyParams->expiryDate;
            /** @var bool $showTermsText */
            $showTermsText = $requestBodyParams->showTermsText;
            /** @var string $termsText */
            $termsText = $requestBodyParams->termsText;
            /** @var int|null $dataPoolConfigId */
            $dataPoolConfigId = $requestBodyParams->dataPoolConfigId ?? null;
            /** @var int[] $elementIds */
            $elementIds = $requestBodyParams->elementIds ?? null;
            /** @var int|null $collectionId */
            $collectionId = $requestBodyParams->collectionId ?? null;
            /** @var Collection|null $collection */
            $collection = null;

            if (empty($name)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.public-share.name-empty'));
            }

            if (!is_array($downloadConfigs)) {
                throw new \Exception('downloadConfigs empty');
            }

            //fix data pool config ids when having language variants
            $fixedDownloadConfigs = [];
            foreach ($downloadConfigs as $downloadDataPoolId => $downloadConfig) {
                $dataPoolConfig = $this->dataPoolConfigService->getDataPoolConfigById($downloadDataPoolId);
                $fixedDownloadConfigs[$dataPoolConfig->getId()] = $downloadConfig;
            }
            $downloadConfigs = $fixedDownloadConfigs;

            /** @var DataPoolConfigInterface|null $dataPoolConfig */
            $dataPoolConfig = null;

            if (is_int($collectionId)) {
                $collection = $this->collectionService->getCollectionById($collectionId);
                if (!$collection) {
                    throw new OutputErrorException($this->translator->trans('portal-engine.collection.not-found'));
                }
            } else {
                $dataPoolConfig = $this->dataPoolConfigService->getDataPoolConfigById($dataPoolConfigId);
                if (!$dataPoolConfig) {
                    throw new \Exception('dataPoolConfig not found');
                }
                $this->dataPoolConfigService->setCurrentDataPoolConfig($dataPoolConfig);

                if (!is_array($elementIds) || empty($elementIds)) {
                    throw new \Exception('elementIds not set or empty');
                }
            }

            /** @var PublicShare $publicShare */
            $publicShare = $this->publicShareService->create(
                $name,
                $downloadConfigs,
                $expiryDate,
                (bool)$showTermsText,
                $termsText,
                $collection
            );

            if (!$collection) {
                /** @var ElementInterface[] $elements */
                $elements = $this->dataPoolConfigService->getElementsByIds($elementIds);

                $this->publicShareService->addItemsToPublicShare($publicShare, $dataPoolConfig, $elements);
            }

            $apiPayload->setData($this->hydratePublicShare($publicShare, ($requestBodyParams->_locale ?? null)));
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route(
     *     "/list",
     *     name="pimcore_portalengine_rest_api_public_share_list"
     * )
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function listAction(Request $request, PaginatorInterface $paginator)
    {
        /** @var \Doctrine\ORM\QueryBuilder $publicShareQuery */
        $publicShareQuery = $this->publicShareService->getPublicShareQuery();

        $pagination = $paginator->paginate($publicShareQuery, $request->get('page', 1), $this->itemCountPerPage);

        $that = $this;

        return new JsonResponse(
            new ApiPayload(BasicListJsonModel::createFromPagination($pagination, function ($item) use ($that, $request) {
                return $that->hydratePublicShare($item, $request->getLocale());
            }))
        );
    }

    /**
     * @Route(
     *     "/update/{publicShareId}",
     *     name="pimcore_portalengine_rest_api_public_share_update",
     *     requirements={"publicShareId"="\d+"}
     * )
     */
    public function updateAction(Request $request, $publicShareId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);
        /** @var \stdClass $requestBodyParams */
        $requestBodyParams = json_decode($request->getContent(), false);

        try {
            /** @var PublicShare|null $publicShare */
            $publicShare = $this->publicShareService->getById($publicShareId);
            if (!$publicShare) {
                throw new OutputErrorException($this->translator->trans('portal-engine.public-share.not-found'));
            }

            if (!$this->publicShareService->isCurrentUserPublicShareOwner($publicShare)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.public-share.edit-not-allowed'));
            }

            /** @var string $name */
            $name = trim($requestBodyParams->name);
            /** @var array $downloadConfigs */
            $downloadConfigs = (array)$requestBodyParams->downloadConfigs;
            /** @var int|null $expiryDate */
            $expiryDate = $requestBodyParams->expiryDate;
            /** @var bool $showTermsText */
            $showTermsText = $requestBodyParams->showTermsText;
            /** @var string $termsText */
            $termsText = (string)$requestBodyParams->termsText;

            if (!is_array($downloadConfigs)) {
                throw new \Exception('downloadConfigs empty');
            }

            $publicShare = $this->publicShareService->updatePublicShare(
                $publicShare,
                $name,
                $downloadConfigs,
                $expiryDate,
                $showTermsText,
                $termsText
            );

            $apiPayload->setData($this->hydratePublicShare($publicShare, ($requestBodyParams->_locale ?? null)));
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @Route(
     *     "/delete/{publicShareId}",
     *     name="pimcore_portalengine_rest_api_public_share_delete",
     *     requirements={"publicShareId"="\d+"}
     * )
     */
    public function deleteAction(Request $request, $publicShareId): JsonResponse
    {
        /** @var ApiPayload $apiPayload */
        $apiPayload = new ApiPayload([]);

        try {
            /** @var PublicShare|null $publicShare */
            $publicShare = $this->publicShareService->getById($publicShareId);
            if (!$publicShare) {
                throw new OutputErrorException($this->translator->trans('portal-engine.public-share.not-found'));
            }

            if (!$this->publicShareService->isCurrentUserPublicShareOwner($publicShare)) {
                throw new OutputErrorException($this->translator->trans('portal-engine.public-share.edit-not-allowed'));
            }

            $this->publicShareService->delete($publicShare);
        } catch (\Exception $e) {
            $apiPayload->handleOutputErrorException($e);
        }

        return new JsonResponse(
            $apiPayload
        );
    }

    /**
     * @param PublicShare $publicShare
     * @param string|null $locale
     *
     * @return array
     *
     * @throws \Exception
     */
    public function hydratePublicShare(PublicShare $publicShare, string $locale = null)
    {
        /** @var PublicShareItem[] $publicShareItems */
        $publicShareItems = $this->publicShareService->getPublicShareItems($publicShare);
        /** @var int $itemCount */
        $itemCount = count($publicShareItems);
        /** @var string $detailUrl */
        $detailUrlParams = ['publicShareHash' => $publicShare->getHash()];
        if ($locale) {
            $languageVariants = $this->languageVariantService->getPortalLanguageVariantsMapping();
            if ($languageVariants[$locale]) {
                $detailUrlParams['_portal_engine_prefix'] = ltrim($languageVariants[$locale], '/') . '/';
            }
        }

        $detailUrl = $this->urlGenerator->generate('pimcore_portalengine_public_share_public_list', $detailUrlParams, UrlGeneratorInterface::ABSOLUTE_URL);

        if ($itemCount === 1 && !$publicShare->getCollectionId()) {
            /** @var PublicShareItem $publicShareItem */
            $publicShareItem = reset($publicShareItems);

            $this->dataPoolConfigService->setCurrentDataPoolConfigById($publicShareItem->getDataPoolId());

            $documentPathDocument = $this->dataPoolConfigService->getCurrentDataPoolConfig()->getLanguageVariantOrDocument();
            if ($locale) {
                $languageVariants = $this->documentService->getTranslations($documentPathDocument);
                $documentPathDocument = $languageVariants[$locale] ? Document::getById($languageVariants[$locale]) : $documentPathDocument;
            }

            switch ($publicShareItem->getElementType()) {
                case ElementType::DATA_OBJECT:
                    $detailUrl = $this->urlGenerator->generate('pimcore_portalengine_public_share_public_object_detail', [
                        'publicShareHash' => $publicShare->getHash(),
                        'id' => $publicShareItem->getElementId(),
                        'documentPath' => ltrim((string)$documentPathDocument, '/'),
                    ], UrlGeneratorInterface::ABSOLUTE_URL);
                    break;
                case ElementType::ASSET:
                    $detailUrl = $this->urlGenerator->generate('pimcore_portalengine_public_share_public_asset_detail', [
                        'publicShareHash' => $publicShare->getHash(),
                        'id' => $publicShareItem->getElementId(),
                        'documentPath' => ltrim((string)$documentPathDocument, '/'),
                    ], UrlGeneratorInterface::ABSOLUTE_URL);
                    break;
            }
        }

        $dataPools = array_values(array_map(function (DataPoolConfigInterface $dataPoolConfig) {
            return [
                'id' => $dataPoolConfig->getId(),
                'name' => $dataPoolConfig->getDataPoolName(),
            ];
        }, $this->publicShareService->getDataPoolConfigsByPublicShare($publicShare)));

        return [
            'id' => $publicShare->getId(),
            'name' => $publicShare->getName(),
            'itemCount' => $itemCount,
            'detailUrl' => $detailUrl,
            'expiryDate' => $publicShare->getExpiryDate(),
            'showTermsText' => $publicShare->isShowTermsText(),
            'termsText' => $publicShare->getTermsText(),
            'configs' => $publicShare->getConfigs(),
            'dataPools' => array_values($dataPools)
        ];
    }
}
