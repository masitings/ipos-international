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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Content;

use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Enum\Routing;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Model\Document;
use Pimcore\Navigation\Page;
use Pimcore\Tool;
use Pimcore\Twig\Extension\NavigationExtension;
use Symfony\Component\Security\Core\Security;

class NavigationService
{
    protected $navigationHelper;
    protected $portalConfigService;
    protected $pageConfigService;
    protected $dataPoolConfigService;
    protected $security;
    protected $documentResolver;

    public function __construct(NavigationExtension $navigationHelper, PortalConfigService $portalConfigService, PageConfigService $pageConfigService, DataPoolConfigService $dataPoolConfigService, Security $security, DocumentResolver $documentResolver)
    {
        $this->navigationHelper = $navigationHelper;
        $this->portalConfigService = $portalConfigService;
        $this->pageConfigService = $pageConfigService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->security = $security;
        $this->documentResolver = $documentResolver;
    }

    /**
     * @param Document $document
     *
     * @return \Pimcore\Navigation\Container
     *
     * @throws \Exception
     */
    public function buildMainNavigation(Document $document)
    {
        return $this->navigationHelper->buildNavigation([
            'active' => $document,
            'root' => $document->getProperty(Routing::NAVIGATION_ROOT_PROPERTY) ?? $this->portalConfigService->getCurrentPortalConfig()->getPortalDocument(),
            'pageCallback' => $this->getMainNavigationPageCallback()
        ]);
    }

    public function isPageVisible(Page $page): bool
    {
        return $page->isVisible()
        && (!$page->getCustomSetting('isDataPoolConfigDocument') || Tool::isFrontendRequestByAdmin() || $this->security->isGranted(Permission::DATA_POOL_ACCESS, $page->getCustomSetting('dataPoolId')));
    }

    public function hasVisibilePages(Page $page): bool
    {
        if (!$page->hasVisiblePages()) {
            return false;
        }

        foreach ($page->getPages() as $subPage) {
            if ($this->isPageVisible($subPage)) {
                return true;
            }
        }

        return false;
    }

    public function getBreadcrumbs(Document $activeDocument = null)
    {
        $activeDocument = $activeDocument ?? $this->documentResolver->getDocument();
        $getNavigationStructure = function ($container, $linkCounter) use (&$getNavigationStructure) {
            $navArray = [];
            foreach ($container as $page) {
                if (!$page instanceof \Pimcore\Navigation\Page\Url) {
                    if ($page->getDocumentType() == 'link') {
                        continue;
                    }
                }
                if ($page->getActive(true)) {
                    $navArray[] = [
                        'uri' => $page->getUri(),
                        'target' => $page->getTarget(),
                        'label' => $page->getLabel()
                    ];

                    $linkCounter++;
                    $lArray = $getNavigationStructure($page->getPages(), $linkCounter);

                    if ($lArray) {
                        foreach ($lArray as $link) {
                            $navArray[] = $link;
                        }
                    }
                }
            }

            return $navArray;
        };

        $linkCounter = 1;
        $root = $activeDocument->getProperty(Routing::NAVIGATION_ROOT_PROPERTY) ?? $this->portalConfigService->getCurrentPortalConfig()->getPortalDocument();
        $pages = $this->navigationHelper->buildNavigation(['active' => $activeDocument, 'root' => $root]);

        return $getNavigationStructure($pages, $linkCounter);
    }

    /**
     * @return \Closure
     */
    protected function getMainNavigationPageCallback()
    {
        return function (\Pimcore\Navigation\Page\Document $page, $document) {
            if (!$document instanceof Document\PageSnippet) {
                return;
            }

            $pageConfig = $this->pageConfigService->createPageConfig($document);

            $isDataPoolConfigDocument = $this->dataPoolConfigService->isDataPoolConfigDocument($document, true);
            $page->setCustomSetting('icon', $pageConfig->getIcon());
            $page->setCustomSetting('isDataPoolConfigDocument', $isDataPoolConfigDocument);
            $page->setCustomSetting(
                'dataPoolId',
                $isDataPoolConfigDocument
                    ? $this->dataPoolConfigService->getDataPoolConfigForDocument($document)->getId()
                    : null
            );
        };
    }
}
