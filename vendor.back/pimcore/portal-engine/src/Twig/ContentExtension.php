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

namespace Pimcore\Bundle\PortalEngineBundle\Twig;

use Pimcore\Bundle\PortalEngineBundle\Service\Content\NavigationService;
use Pimcore\Bundle\PortalEngineBundle\Service\Document\PrefixService;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Document;
use Pimcore\Navigation\Page;
use Pimcore\Navigation\Renderer\Breadcrumbs;
use Pimcore\Templating\Renderer\IncludeRenderer;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContentExtension extends AbstractExtension
{
    protected $kernel;
    protected $navigationService;
    protected $includeRenderer;
    protected $prefixService;
    protected $breadcrumbs;
    protected $statisticsExplorerEnabled = false;

    public function __construct(KernelInterface $kernel, NavigationService $navigationService, IncludeRenderer $includeRenderer, PrefixService $prefixService, Breadcrumbs $breadcrumbs)
    {
        $this->kernel = $kernel;
        $this->navigationService = $navigationService;
        $this->includeRenderer = $includeRenderer;
        $this->prefixService = $prefixService;
        $this->breadcrumbs = $breadcrumbs;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('portalEngine_main_navigation', [$this, 'getMainNavigation']),
            new TwigFunction('portalEngine_inline_icon', [$this, 'getInlineIcon'], ['is_safe' => ['html']]),
            new TwigFunction('portalEngine_navigation_visible', [$this, 'getNavigationVisible'], ['is_safe' => ['html']]),
            new TwigFunction('portalEngine_navigation_hasVisiblePages', [$this, 'getNavigationHasVisiblePages'], ['is_safe' => ['html']]),
            new TwigFunction('portalEngine_localizedInc', [$this, 'localizedInc'], ['is_safe' => ['html']]),
            new TwigFunction('portalEngine_get_breadcrumbs', [$this, 'getBreadcrumbs'], ['is_safe' => ['html']]),
            new TwigFunction('portalEngine_addClassToLnk', [$this, 'addClassToLink']),
            new TwigFunction('portalEngine_enableStatisticsExplorer', [$this, 'enableStatisticsExplorer']),
            new TwigFunction('portalEngine_isStatisticsExplorerEnabled', [$this, 'isStatisticsExplorerEnabled']),
        ];
    }

    /**
     * @param Document $document
     *
     * @return \Pimcore\Navigation\Container
     *
     * @throws \Exception
     */
    public function getMainNavigation(Document $document)
    {
        return $this->navigationService->buildMainNavigation($document);
    }

    public function getBreadcrumbs(Document $document)
    {
        return $this->navigationService->getBreadcrumbs($document);
    }

    /**
     * @param string ...$paths
     *
     * @return string
     */
    protected function joinPaths(...$paths)
    {
        return implode(DIRECTORY_SEPARATOR, $paths);
    }

    /**
     * @return false|string
     */
    public function getIconDirectory()
    {
        return realpath($this->kernel->locateResource('@PimcorePortalEngineBundle') . $this->joinPaths('..', 'assets', 'icons'));
    }

    /**
     * @param Image|string $icon
     *
     * @return string|null
     */
    public function getInlineIcon($icon): ?string
    {
        try {
            if ($icon instanceof Image) {
                return $this->extractInlineIconFileFromAsset($icon);
            } elseif (is_string($icon)) {
                return $this->extractInlineIconFileFromIcon($icon);
            }
        } catch (\Exception $e) {
        }

        return null;
    }

    public function getNavigationVisible(Page $page)
    {
        return $this->navigationService->isPageVisible($page);
    }

    public function getNavigationHasVisiblePages(Page $page)
    {
        return $this->navigationService->hasVisibilePages($page);
    }

    public function localizedInc($include, array $params = [], $cacheEnabled = true, $editmode = null)
    {
        if ($include instanceof Document) {
            $include = $this->prefixService->getLocalizedVersionByRelativePath($include);
        }

        return $this->includeRenderer->render($include, $params, $cacheEnabled, $editmode);
    }

    public function addClassToLink(?Document\Editable\Link $link, string $class)
    {
        if (empty($link)) {
            return null;
        }

        $data = $link->getDataEditmode();
        if (!empty($data['class'])) {
            $data['class'] .= ' ';
        }
        $data['class'] .= $class;

        $link->setDataFromEditmode($data);

        return $link;
    }

    /**
     * @param string $icon
     *
     * @return string
     */
    protected function extractInlineIconFileFromIcon(string $icon)
    {
        // force .svg at the end
        $icon = str_replace('.svg', '', $icon);
        $file = $this->joinPaths($this->getIconDirectory(), "{$icon}.svg");
        if (file_exists($file)) {
            return file_get_contents($file);
        }

        return null;
    }

    /**
     * @param Image $icon
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function extractInlineIconFileFromAsset(Image $icon)
    {
        if ($icon->getMimetype() !== 'image/svg+xml') {
            throw new \Exception('Asset is not a svg.');
        }

        return stream_get_contents($icon->getStream());
    }

    public function enableStatisticsExplorer()
    {
        $this->statisticsExplorerEnabled = true;
    }

    public function isStatisticsExplorerEnabled(): bool
    {
        return $this->statisticsExplorerEnabled;
    }
}
