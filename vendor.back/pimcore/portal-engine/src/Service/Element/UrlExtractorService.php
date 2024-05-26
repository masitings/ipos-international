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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Element;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset;
use Pimcore\Bundle\PortalEngineBundle\Service\Content\NavigationService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlExtractorService
{
    protected $dataPoolConfigService;
    protected $nameExtractorService;
    protected $dataObjectUrlExtractorService;
    protected $assetUrlExtractorService;
    protected $publicShareService;
    protected $urlGenerator;
    protected $navigationService;
    protected $requestStack;

    public function __construct(
        DataPoolConfigService $dataPoolConfigService,
        NameExtractorService $nameExtractorService,
        DataObject\UrlExtractorService $dataObjectUrlExtractorService,
        Asset\UrlExtractorService $assetUrlExtractorService,
        PublicShareService $publicShareService,
        UrlGeneratorInterface $urlGenerator,
        NavigationService $navigationService,
        RequestStack $requestStack
    ) {
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->nameExtractorService = $nameExtractorService;
        $this->dataObjectUrlExtractorService = $dataObjectUrlExtractorService;
        $this->assetUrlExtractorService = $assetUrlExtractorService;
        $this->publicShareService = $publicShareService;
        $this->urlGenerator = $urlGenerator;
        $this->navigationService = $navigationService;
        $this->requestStack = $requestStack;
    }

    /**
     * @param ElementInterface $element
     * @param DataPoolConfigInterface $dataPoolConfig
     *
     * @return string
     */
    public function extractUrl(ElementInterface $element, ?DataPoolConfigInterface $dataPoolConfig = null)
    {
        if ($element->getType() === 'folder') {
            $dataPoolConfig = $dataPoolConfig ?: $this->dataPoolConfigService->getCurrentDataPoolConfig();

            /*
             * Frontend javascript currently has a strange bug -> it accepts // at the beginning only.
             * Remove the additional slash as soon as the bug is finished.
             */
            return $dataPoolConfig->getLanguageVariantOrDocument() . '?folder=' . urlencode('/' .$element->getRealFullPath());
        }

        if ($element instanceof AbstractObject) {
            return $this->dataObjectUrlExtractorService->extractUrl($element, $dataPoolConfig);
        } elseif ($element instanceof \Pimcore\Model\Asset) {
            return $this->assetUrlExtractorService->extractUrl($element, $dataPoolConfig);
        }

        return null;
    }

    /**
     * @param ElementInterface $element
     * @param DataPoolConfigInterface $dataPoolConfig
     *
     * @return array
     */
    public function extractBreadcrumbs(ElementInterface $element, ?DataPoolConfigInterface $dataPoolConfig = null)
    {
        $dataPoolConfig = $dataPoolConfig ?: $this->dataPoolConfigService->getCurrentDataPoolConfig();

        $breadcrumbs = [];

        $params = array_filter($this->requestStack->getCurrentRequest()->query->all(), function ($key) {
            return !in_array($key, ['id', 'dataPoolId', 'documentId']);
        }, ARRAY_FILTER_USE_KEY);

        if ($publicShare = $this->publicShareService->getCurrentPublicShare()) {
            $params['publicShareHash'] = $publicShare->getHash();
            $breadcrumbs[] = [
                'label' => $dataPoolConfig->getDataPoolName(),
                'url' => $this->urlGenerator->generate('pimcore_portalengine_public_share_public_list', $params)
            ];
        } elseif ($document = $dataPoolConfig->getLanguageVariantOrDocument()) {
            $query = http_build_query($params);
            $navigationBreadcrumbs = $this->navigationService->getBreadcrumbs($dataPoolConfig->getLanguageVariantOrDocument());
            foreach ($navigationBreadcrumbs as $i => $breadcrumb) {
                if ($i == sizeof($navigationBreadcrumbs) - 1) {
                    $breadcrumb['uri'] .= '?' . $query;
                }

                $breadcrumbs[] = [
                    'label' => $breadcrumb['label'],
                    'url' => $breadcrumb['uri']
                ];
            }
        }

        $breadcrumbs[] = [
            'label' => $this->nameExtractorService->extractName($element),
            'url' => $this->extractUrl($element)
        ];

        return $breadcrumbs;
    }
}
